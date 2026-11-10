<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Requests\Admin\ProductCategoryRequest;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

final class ProductCategoryController extends CrudController
{
    protected string $model = ProductCategory::class;

    protected string $resource = ProductCategoryResource::class;

    protected string $formRequest = ProductCategoryRequest::class;

    protected array $search = ['name_ar', 'name_en'];

    protected array $filters = [];

    protected array $with = [];

    protected array $withCount = ['types'];

    #[OA\Get(
        path: '/api/v1/admin/product-categories',
        summary: 'List product categories',
        security: [['sanctum' => []]],
        tags: ['Admin · Product categories'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/search'),
            new OA\Parameter(ref: '#/components/parameters/page'),
            new OA\Parameter(ref: '#/components/parameters/per_page'),
            new OA\Parameter(ref: '#/components/parameters/is_active'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ProductCategoryAdmin')),
                new OA\Property(property: 'meta', ref: '#/components/schemas/PaginationMeta'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ]
    )]
    public function index(Request $request): AnonymousResourceCollection
    {
        return $this->indexAction($request);
    }

    #[OA\Post(
        path: '/api/v1/admin/product-categories',
        summary: 'Create a product categories record',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: '#/components/schemas/ProductCategoryInput')
        )),
        tags: ['Admin · Product categories'],
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ProductCategoryAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function store(): JsonResponse
    {
        return $this->storeAction();
    }

    #[OA\Get(
        path: '/api/v1/admin/product-categories/{id}',
        summary: 'Show one product categories record',
        security: [['sanctum' => []]],
        tags: ['Admin · Product categories'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ProductCategoryAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        ]
    )]
    public function show(int $id): ProductCategoryResource
    {
        return $this->showAction($id);
    }

    #[OA\Post(
        path: '/api/v1/admin/product-categories/{id}',
        summary: 'Update a product categories record (send only changed fields; PUT/PATCH also accepted for JSON)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: '#/components/schemas/ProductCategoryInput')
        )),
        tags: ['Admin · Product categories'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ProductCategoryAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function update(int $id): ProductCategoryResource
    {
        return $this->updateAction($id);
    }

    #[OA\Delete(
        path: '/api/v1/admin/product-categories/{id}',
        summary: 'Delete a product categories record',
        security: [['sanctum' => []]],
        tags: ['Admin · Product categories'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        return $this->destroyAction($id);
    }

    #[OA\Post(
        path: '/api/v1/admin/product-categories/reorder',
        summary: 'Reorder product categories',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReorderInput')),
        tags: ['Admin · Product categories'],
        responses: [new OA\Response(response: 204, description: 'Reordered')]
    )]
    public function reorder(Request $request): JsonResponse
    {
        return $this->reorderAction($request);
    }
}
