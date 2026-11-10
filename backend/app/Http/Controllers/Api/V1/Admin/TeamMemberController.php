<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Requests\Admin\TeamMemberRequest;
use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

final class TeamMemberController extends CrudController
{
    protected string $model = TeamMember::class;

    protected string $resource = TeamMemberResource::class;

    protected string $formRequest = TeamMemberRequest::class;

    protected array $search = ['name_ar', 'name_en'];

    protected array $filters = [];

    protected array $with = [];

    protected array $withCount = [];

    #[OA\Get(
        path: '/api/v1/admin/team-members',
        summary: 'List team members',
        security: [['sanctum' => []]],
        tags: ['Admin · Team members'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/search'),
            new OA\Parameter(ref: '#/components/parameters/page'),
            new OA\Parameter(ref: '#/components/parameters/per_page'),
            new OA\Parameter(ref: '#/components/parameters/is_active'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paginated list', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/TeamMemberAdmin')),
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
        path: '/api/v1/admin/team-members',
        summary: 'Create a team members record',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: '#/components/schemas/TeamMemberInput')
        )),
        tags: ['Admin · Team members'],
        responses: [
            new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/TeamMemberAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function store(): JsonResponse
    {
        return $this->storeAction();
    }

    #[OA\Get(
        path: '/api/v1/admin/team-members/{id}',
        summary: 'Show one team members record',
        security: [['sanctum' => []]],
        tags: ['Admin · Team members'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/TeamMemberAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        ]
    )]
    public function show(int $id): TeamMemberResource
    {
        return $this->showAction($id);
    }

    #[OA\Post(
        path: '/api/v1/admin/team-members/{id}',
        summary: 'Update a team members record (send only changed fields; PUT/PATCH also accepted for JSON)',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(ref: '#/components/schemas/TeamMemberInput')
        )),
        tags: ['Admin · Team members'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/TeamMemberAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function update(int $id): TeamMemberResource
    {
        return $this->updateAction($id);
    }

    #[OA\Delete(
        path: '/api/v1/admin/team-members/{id}',
        summary: 'Delete a team members record',
        security: [['sanctum' => []]],
        tags: ['Admin · Team members'],
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
        path: '/api/v1/admin/team-members/reorder',
        summary: 'Reorder team members',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ReorderInput')),
        tags: ['Admin · Team members'],
        responses: [new OA\Response(response: 204, description: 'Reordered')]
    )]
    public function reorder(Request $request): JsonResponse
    {
        return $this->reorderAction($request);
    }
}
