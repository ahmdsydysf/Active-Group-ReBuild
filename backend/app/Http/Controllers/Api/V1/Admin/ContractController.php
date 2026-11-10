<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Requests\Admin\ContractRequest;
use App\Http\Resources\ContractResource;
use App\Models\Contract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

final class ContractController extends CrudController
{
    protected string $model = Contract::class;

    protected string $resource = ContractResource::class;

    protected string $formRequest = ContractRequest::class;

    protected array $search = ['title_ar', 'title_en'];

    protected array $filters = [];

    protected array $with = [];

    protected array $withCount = [];

    #[OA\Get(
        path: '/api/v1/admin/contracts',
        summary: 'List contracts',
        security: [['sanctum' => []]],
        tags: ['Admin · Contracts'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/search'),
            new OA\Parameter(ref: '#/components/parameters/page'),
            new OA\Parameter(ref: '#/components/parameters/per_page'),
            new OA\Parameter(ref: '#/components/parameters/is_active'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/ContractAdmin')),
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
        path: '/api/v1/admin/contracts',
        summary: 'Create a contracts record',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: '#/components/schemas/ContractInput')
        )),
        tags: ['Admin · Contracts'],
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ContractAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function store(): JsonResponse
    {
        return $this->storeAction();
    }

    #[OA\Get(
        path: '/api/v1/admin/contracts/{id}',
        summary: 'Show one contracts record',
        security: [['sanctum' => []]],
        tags: ['Admin · Contracts'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ContractAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        ]
    )]
    public function show(int $id): ContractResource
    {
        return $this->showAction($id);
    }

    #[OA\Post(
        path: '/api/v1/admin/contracts/{id}',
        summary: 'Update a contracts record (send only changed fields; PUT/PATCH also accepted for JSON)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: '#/components/schemas/ContractInput')
        )),
        tags: ['Admin · Contracts'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/ContractAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function update(int $id): ContractResource
    {
        return $this->updateAction($id);
    }

    #[OA\Delete(
        path: '/api/v1/admin/contracts/{id}',
        summary: 'Delete a contracts record',
        security: [['sanctum' => []]],
        tags: ['Admin · Contracts'],
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
        path: '/api/v1/admin/contracts/reorder',
        summary: 'Reorder contracts',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReorderInput')),
        tags: ['Admin · Contracts'],
        responses: [new OA\Response(response: 204, description: 'Reordered')]
    )]
    public function reorder(Request $request): JsonResponse
    {
        return $this->reorderAction($request);
    }
}
