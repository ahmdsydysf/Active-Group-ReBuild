<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageStore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * List, create, update, delete and reorder for one content model.
 * A concrete controller names the model and its validation rules; image columns
 * (Model::IMAGES) are validated, re-encoded and cleaned up here.
 *
 * Images are sent as multipart fields named like the column; `remove_<column>=1` clears one.
 * Updates with files use POST + `_method=PUT` because PHP does not parse multipart PUT bodies.
 *
 * @template TModel of Model
 */
abstract class ContentController extends Controller
{
    /** @var class-string<TModel> */
    protected string $model;

    /** Columns matched by `?search=`. */
    protected array $search = [];

    /** Image columns that must exist after saving (the site never shows a card without its image). */
    protected array $requiredImages = [];

    /** @return array<string, mixed> */
    abstract protected function rules(?Model $record): array;

    public function index(Request $request): JsonResponse
    {
        $query = $this->model::query()->ordered();

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

    public function store(Request $request): JsonResponse
    {
        $record = new $this->model;
        $this->save($record, $request);

        return response()->json(['data' => $this->present($record)], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $record = $this->find($id);
        $this->save($record, $request);

        return response()->json(['data' => $this->present($record->refresh())]);
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

        return response()->json(null, 204);
    }

    /** @return TModel */
    protected function find(int $id): Model
    {
        return $this->model::query()->findOrFail($id);
    }

    protected function save(Model $record, Request $request): void
    {
        $images = $this->imageFields();
        $rules = $this->rules($record->exists ? $record : null);

        foreach ($images as $field) {
            $required = in_array($field, $this->requiredImages, true) && ! $record->getAttribute($field);
            $rules[$field] = [$required ? 'required' : 'nullable', 'file', ...explode('|', ImageStore::MIMES)];
            $rules["remove_{$field}"] = 'nullable|boolean';
        }

        $data = $request->validate($rules);
        $old = [];

        foreach ($images as $field) {
            unset($data[$field], $data["remove_{$field}"]);
            $file = $request->file($field);

            if ($file instanceof UploadedFile) {
                $old[] = $record->getAttribute($field);
                $record->setAttribute($field, $this->storeImage($file, $field));
            } elseif ($request->boolean("remove_{$field}") && ! in_array($field, $this->requiredImages, true)) {
                $old[] = $record->getAttribute($field);
                $record->setAttribute($field, null);
            }
        }

        $record->fill($data);

        if (! $record->exists && $record->isFillable('sort_order') && ! isset($data['sort_order'])) {
            $record->setAttribute('sort_order', (int) $this->model::query()->max('sort_order') + 1);
        }

        $record->save();

        // Old files go only after the new row is saved.
        array_map(ImageStore::delete(...), array_filter($old));
    }

    /** @return list<string> */
    protected function imageFields(): array
    {
        return defined("{$this->model}::IMAGES") ? $this->model::IMAGES : [];
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

    /** Every column for editing, image URLs, and whether any English field is missing. */
    protected function present(Model $record): array
    {
        $data = $record->attributesToArray();

        foreach ($this->imageFields() as $field) {
            $data["{$field}_url"] = ImageStore::url($record->getAttribute($field), true);
        }

        $data['missing_en'] = collect($data)
            ->filter(fn ($v, $k) => str_ends_with((string) $k, '_ar') && filled($v))
            ->keys()
            ->contains(fn ($k) => blank($data[substr($k, 0, -3).'_en'] ?? null));

        return $data;
    }
}
