<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/** Swagger docs for ContentController::reorder(). */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AdminReorder extends OA\Post
{
    public function __construct(string $path, string $tag)
    {
        parent::__construct(
            path: "/api/v1/admin/{$path}/reorder",
            operationId: 'reorder-'.$path,
            summary: 'Save a new display order',
            security: [['sanctum' => []]],
            requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
                required: ['ids'],
                properties: [
                    new OA\Property(property: 'ids', description: 'Record ids top to bottom', type: 'array', items: new OA\Items(type: 'integer')),
                ]
            )),
            tags: [$tag],
            responses: [
                new OA\Response(response: 204, description: 'Done'),
                new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
            ]
        );
    }
}
