<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/** Swagger docs for ContentController::bulk(). */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AdminBulk extends OA\Post
{
    public function __construct(string $path, string $tag)
    {
        parent::__construct(
            path: "/api/v1/admin/{$path}/bulk",
            operationId: 'bulk-'.$path,
            summary: 'Publish, hide or delete several records',
            security: [['sanctum' => []]],
            requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
                required: ['ids', 'action'],
                properties: [
                    new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'integer')),
                    new OA\Property(property: 'action', type: 'string', enum: ['publish', 'hide', 'delete']),
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
