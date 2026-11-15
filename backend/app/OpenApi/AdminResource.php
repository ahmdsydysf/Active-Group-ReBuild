<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/**
 * Swagger docs for a ContentController collection: list and create on /admin/{path}.
 * Pair with AdminRecord (/{id}) and AdminActions (reorder, bulk).
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AdminResource extends OA\PathItem
{
    public function __construct(string $path, string $tag, string $schema, array $filters = [])
    {
        $query = fn (string $name, string $type = 'string') => new OA\Parameter(name: $name, in: 'query', required: false, schema: new OA\Schema(type: $type));

        parent::__construct(
            path: "/api/v1/admin/{$path}",
            get: new OA\Get(
                operationId: 'list-'.$path,
                summary: 'List records in display order',
                security: [['sanctum' => []]],
                tags: [$tag],
                parameters: [$query('search'), $query('page', 'integer'), $query('per_page', 'integer'), ...array_map($query, $filters)],
                responses: [
                    new OA\Response(response: 200, description: 'Records', content: new OA\JsonContent(properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: "#/components/schemas/{$schema}")),
                        new OA\Property(property: 'meta', ref: '#/components/schemas/PageMeta'),
                    ])),
                    new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
                ],
            ),
            post: new OA\Post(
                operationId: 'create-'.$path,
                summary: 'Create a record. Files go as multipart fields named like their column',
                security: [['sanctum' => []]],
                requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(ref: "#/components/schemas/{$schema}")
                )),
                tags: [$tag],
                responses: [
                    new OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(properties: [
                        new OA\Property(property: 'data', ref: "#/components/schemas/{$schema}"),
                    ])),
                    new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
                ],
            ),
        );
    }
}
