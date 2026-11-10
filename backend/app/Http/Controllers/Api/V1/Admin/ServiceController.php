<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Requests\Admin\ServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

final class ServiceController extends CrudController
{
    protected string $model = Service::class;

    protected string $resource = ServiceResource::class;

    protected string $formRequest = ServiceRequest::class;

    protected array $search = ['title_ar', 'title_en'];

    protected array $filters = ['service_type_id'];

    protected array $with = ['type'];

    protected array $withCount = [];

    #[OA\Get(
        path: '/api/v1/admin/services',
        summary: 'List services & advice',
        security: [['sanctum' => []]],
        tags: ['Admin · Services & advice'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/search'),
            new OA\Parameter(ref: '#/components/parameters/page'),
            new OA\Parameter(ref: '#/components/parameters/per_page'),
            new OA\Parameter(ref: '#/components/parameters/is_active'),
            new OA\Parameter(name: 'service_type_id', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ServiceAdmin')),
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
        path: '/api/v1/admin/services',
        summary: 'Create a services & advice record',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: '#/components/schemas/ServiceInput')
        )),
        tags: ['Admin · Services & advice'],
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ServiceAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function store(): JsonResponse
    {
        return $this->storeAction();
    }

    #[OA\Get(
        path: '/api/v1/admin/services/{id}',
        summary: 'Show one services & advice record',
        security: [['sanctum' => []]],
        tags: ['Admin · Services & advice'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ServiceAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        ]
    )]
    public function show(int $id): ServiceResource
    {
        return $this->showAction($id);
    }

    #[OA\Post(
        path: '/api/v1/admin/services/{id}',
        summary: 'Update a services & advice record (send only changed fields; PUT/PATCH also accepted for JSON)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: '#/components/schemas/ServiceInput')
        )),
        tags: ['Admin · Services & advice'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ServiceAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function update(int $id): ServiceResource
    {
        return $this->updateAction($id);
    }

    #[OA\Delete(
        path: '/api/v1/admin/services/{id}',
        summary: 'Delete a services & advice record',
        security: [['sanctum' => []]],
        tags: ['Admin · Services & advice'],
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
        path: '/api/v1/admin/services/reorder',
        summary: 'Reorder services & advice',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReorderInput')),
        tags: ['Admin · Services & advice'],
        responses: [new OA\Response(response: 204, description: 'Reordered')]
    )]
    public function reorder(Request $request): JsonResponse
    {
        return $this->reorderAction($request);
    }
}
