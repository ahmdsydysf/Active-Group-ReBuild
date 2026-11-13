<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/** Swagger docs for ContentController::destroy() on a content controller. */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AdminDelete extends OA\Delete
{
    public function __construct(string $path, string $tag, string $schema)
    {
        parent::__construct(
            path: "/api/v1/admin/{$path}/{id}",
            summary: 'Delete a record and its images',
            security: [['sanctum' => []]],
            tags: [$tag],
            parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
            responses: [
                new OA\Response(response: 204, description: 'Deleted'),
                new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            ]
        );
    }
}
