<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/** Swagger docs for ContentController::index() on a content controller. */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AdminList extends OA\Get
{
    public function __construct(string $path, string $tag, string $schema)
    {
        parent::__construct(
            path: "/api/v1/admin/{$path}",
            summary: 'List records in display order',
            security: [['sanctum' => []]],
            tags: [$tag],
            parameters: [
                new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
                new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
                new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', maximum: 200)),
            ],
            responses: [
                new OA\Response(response: 200, description: 'Records', content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: "#/components/schemas/{$schema}")),
                    new OA\Property(property: 'meta', ref: '#/components/schemas/PageMeta'),
                ])),
                new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
            ]
        );
    }
}
