<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageStore;
use App\Support\Publisher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * List, create, update, delete, reorder and bulk actions for one content model.
 * A concrete controller names the model and its validation rules; uploads are handled here:
 * image columns (Model::IMAGES) are re-encoded to WebP, other files (Model::FILES) are stored as sent.
 *
 * Files are sent as multipart fields named like the column; `remove_<column>=1` clears one.
 * Updates with files use POST + `_method=PUT` because PHP does not parse multipart PUT bodies.
 * List fields (repeaters) may arrive as JSON strings in multipart requests; see $jsonFields.
 *
 * @template TModel of Model
 */
abstract class ContentController extends Controller
{
    /** @var class-string<TModel> */
    protected string $model;

    /** Columns matched by `?search=`. */
    protected array $search = [];

    /** Relations loaded for the list and for responses. */
    protected array $with = [];

    /** Upload columns that must exist after saving (the site never shows a card without its image). */
    protected array $requiredImages = [];

    /** Array inputs that multipart forms send as JSON text. */
    protected array $jsonFields = [];

    /** @return array<string, mixed> */
    abstract protected function rules(?Model $record): array;

    /** Extra list filters from the query string. */
    protected function filter(Builder $query, Request $request): void {}

    /** Default list order. */
    protected function order(Builder $query): void
    {
        $query->ordered();
    }

    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query()->with($this->with);
        $this->order($query);
        $this->filter($query, $request);

        if ($term = trim((string) $request->query('search'))) {
            $query->where(function (Builder $q) use ($term): void {
                foreach ($this->search as $column) {
                    $q->orWhere($column, 'like', '%'.addcslashes($term, '%_\\').'%');
                }
            });
        }

        $page = $query->paginate(min(max((int) $request->query('per_page', 100), 1), 200));

        return response()->json([
            'data' => $page->getCollection()->map(fn (Model $m) => $this->present($m))->values(),
            'meta' => ['total' => $page->total(), 'page' => $page->currentPage(), 'last_page' => $page->lastPage()],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => $this->present($this->find($id)->load($this->with))]);
    }

    public function store(Request $request): JsonResponse
    {
        $record = new $this->model;
        $this->save($record, $request);

        return response()->json(['data' => $this->present($record->refresh()->load($this->with))], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $record = $this->find($id);
        $this->save($record, $request);

        return response()->json(['data' => $this->present($record->refresh()->load($this->with))]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->find($id)->delete();

        return response()->json(null, 204);
    }

    /** Body: `{ "ids": [3, 1, 2] }`, the new order top to bottom. */
    public function reorder(Request $request): JsonResponse
    {
        $ids = $request->validate(['ids' => 'required|array|max:500', 'ids.*' => 'integer|distinct'])['ids'];

        DB::transaction(function () use ($ids): void {
            foreach (array_values($ids) as $position => $id) {
                $this->model::query()->whereKey($id)->update(['sort_order' => $position]);
            }
        });
        Publisher::touch();

        return response()->json(null, 204);
    }

    /** Body: `{ "ids": [...], "action": "publish" | "hide" | "delete" }`. */
    public function bulk(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => 'required|array|max:200',
            'ids.*' => 'integer',
            'action' => 'required|in:publish,hide,delete',
        ]);

        // One by one so model events run: files are removed and the site is marked for publishing.
        DB::transaction(function () use ($data): void {
            $this->model::query()->whereKey($data['ids'])->get()->each(fn (Model $m) => $data['action'] === 'delete'
                ? $m->delete()
                : $m->update(['is_active' => $data['action'] === 'publish']));
        });

        return response()->json(null, 204);
    }

    /** @return TModel */
    protected function find(int $id): Model
    {
        return $this->model::query()->findOrFail($id);
    }

    protected function save(Model $record, Request $request): void
    {
        foreach ($this->jsonFields as $field) {
            if (is_string($request->input($field))) {
                $request->merge([$field => json_decode($request->input($field), true) ?? []]);
            }
        }

        $uploads = $this->uploadRules($record);
        $data = $request->validate([...$this->rules($record->exists ? $record : null), ...$uploads]);
        $old = [];

        foreach ($this->uploadFields() as $field => $isImage) {
            unset($data[$field], $data["remove_{$field}"]);
            $file = $request->file($field);

            if ($file instanceof UploadedFile) {
                $old[$field] = $record->getAttribute($field);
                $record->setAttribute($field, $isImage ? $this->storeImage($file, $field) : $file->storeAs($this->model::UPLOAD_DIR, Str::lower((string) Str::ulid()).'.'.$file->extension(), 'public'));
            } elseif ($request->boolean("remove_{$field}") && ! in_array($field, $this->requiredImages, true)) {
                $old[$field] = $record->getAttribute($field);
                $record->setAttribute($field, null);
            }
        }

        $record->fill($data);

        if (! $record->exists && $record->isFillable('sort_order') && ! isset($data['sort_order'])) {
            $record->setAttribute('sort_order', (int) $this->model::query()->max('sort_order') + 1);
        }

        $record->save();

        // Old files go only after the new row is saved.
        foreach (array_filter($old) as $field => $path) {
            $this->uploadFields()[$field] ? ImageStore::delete($path) : Storage::disk('public')->delete($path);
        }
    }

    /** Upload columns: name => true for images, false for other files. */
    protected function uploadFields(): array
    {
        $images = defined("{$this->model}::IMAGES") ? $this->model::IMAGES : [];
        $files = defined("{$this->model}::FILES") ? array_keys($this->model::FILES) : [];

        return [...array_fill_keys($images, true), ...array_fill_keys($files, false)];
    }

    private function uploadRules(Model $record): array
    {
        $rules = [];
        foreach ($this->uploadFields() as $field => $isImage) {
            $required = in_array($field, $this->requiredImages, true) && ! $record->getAttribute($field);
            $type = $isImage ? explode('|', ImageStore::MIMES) : ['mimes:'.$this->model::FILES[$field][0], 'max:'.$this->model::FILES[$field][1]];
            $rules[$field] = [$required ? 'required' : 'nullable', 'file', ...$type];
            $rules["remove_{$field}"] = 'nullable|boolean';
        }

        return $rules;
    }

    /** A file that passes the type check but GD cannot decode is a validation error, not a crash. */
    private function storeImage(UploadedFile $file, string $field): string
    {
        try {
            return ImageStore::store($file, $this->model::UPLOAD_DIR);
        } catch (RuntimeException) {
            throw ValidationException::withMessages([$field => __('validation.image', ['attribute' => $field])]);
        }
    }

    /** Every column for editing, upload URLs, and whether any English field is missing. */
    protected function present(Model $record): array
    {
        $data = $record->attributesToArray();

        foreach ($this->uploadFields() as $field => $isImage) {
            $path = $record->getAttribute($field);
            $data["{$field}_url"] = $isImage ? ImageStore::url($path, true) : ($path ? Storage::disk('public')->url($path) : null);
        }

        $data['missing_en'] = collect($data)
            ->filter(fn ($v, $k) => str_ends_with((string) $k, '_ar') && is_string($v) && filled($v))
            ->keys()
            ->contains(fn ($k) => blank($data[substr($k, 0, -3).'_en'] ?? null));

        return $data;
    }
}
