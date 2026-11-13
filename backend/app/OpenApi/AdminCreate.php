<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/** Swagger docs for ContentController::store() on a content controller. */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AdminCreate extends OA\Post
{
    public function __construct(string $path, string $tag, string $schema)
    {
        parent::__construct(
            path: "/api/v1/admin/{$path}",
            summary: 'Create a record (multipart when it carries an image)',
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
            ]
        );
    }
}
