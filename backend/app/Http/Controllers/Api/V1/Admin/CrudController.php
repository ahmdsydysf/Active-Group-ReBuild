<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Shared CRUD behaviour for every admin content resource.
 * Concrete controllers only declare the model, resource, form request and OpenAPI docs.
 */
abstract class CrudController extends Controller
{
    /** @var class-string<Model> */
    protected string $model;

    /** @var class-string<JsonResource> */
    protected string $resource;

    /** @var class-string<FormRequest> */
    protected string $formRequest;

    /** @var list<string> */
    protected array $search = [];

    /** @var list<string> */
    protected array $filters = [];

    /** @var list<string> */
    protected array $with = [];

    /** @var list<string> */
    protected array $withCount = [];

    protected function query(): Builder
    {
        return $this->model::query()->with($this->with)->withCount($this->withCount);
    }

    protected function indexAction(Request $request): AnonymousResourceCollection
    {
        $query = $this->query();

        if ($term = trim((string) $request->query('search'))) {
            $query->where(function (Builder $q) use ($term): void {
                foreach ($this->search as $column) {
                    $q->orWhere($column, 'like', "%{$term}%");
                }
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        foreach ($this->filters as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->query($filter));
            }
        }

        $query->orderBy('sort_order')->orderByDesc('id');

        $perPage = min(max((int) $request->query('per_page', 20), 1), 200);

        return $this->resource::collection($query->paginate($perPage)->withQueryString());
    }

    protected function showAction(int $id): JsonResource
    {
        return new $this->resource($this->query()->findOrFail($id));
    }

    protected function storeAction(): JsonResponse
    {
        $request = app($this->formRequest);
        $model = new $this->model;

        DB::transaction(fn () => $this->fillAndSave($model, $request));

        return (new $this->resource($model->load($this->with)))->response()->setStatusCode(201);
    }

    protected function updateAction(int $id): JsonResource
    {
        $model = $this->model::query()->findOrFail($id);
        $request = app($this->formRequest);

        DB::transaction(fn () => $this->fillAndSave($model, $request));

        return new $this->resource($model->refresh()->load($this->with));
    }

    protected function destroyAction(int $id): JsonResponse
    {
        $model = $this->model::query()->findOrFail($id);
        $model->delete();
        $model->deleteFiles();

        return response()->json(null, 204);
    }

    protected function reorderAction(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => 'required|array|max:500',
            'items.*.id' => 'required|integer',
            'items.*.sort_order' => 'required|integer',
        ]);

        DB::transaction(function () use ($data): void {
            foreach ($data['items'] as $item) {
                $this->model::query()->whereKey($item['id'])->first()?->update(['sort_order' => $item['sort_order']]);
            }
        });

        return response()->json(null, 204);
    }

    protected function fillAndSave(Model $model, FormRequest $request): void
    {
        $data = $request->validated();
        $fileFields = $model->fileFields ?? [];

        foreach ($fileFields as $field) {
            unset($data[$field], $data["remove_{$field}"]);
        }

        foreach ($data as $key => $value) {
            if (is_string($value) && $this->isHtmlColumn($model, $key)) {
                $data[$key] = HtmlSanitizer::clean($value);
            }
        }

        $model->fill($data);

        foreach ($fileFields as $field) {
            $file = $request->file($field);
            if ($file instanceof UploadedFile) {
                $this->deleteStoredFile($model->getAttribute($field));
                $model->setAttribute($field, $file->store($model::UPLOAD_DIR, 'public'));
            } elseif ($request->boolean("remove_{$field}")) {
                $this->deleteStoredFile($model->getAttribute($field));
                $model->setAttribute($field, null);
            }
        }

        $model->save();
    }

    private function isHtmlColumn(Model $model, string $column): bool
    {
        return in_array($column, $model->htmlFields ?? [], true);
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path && ! str_starts_with($path, 'http')) {
            Storage::disk('public')->delete($path);
        }
    }
}
