<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/** Swagger docs for ContentController::update() on a content controller. */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AdminUpdate extends OA\Put
{
    public function __construct(string $path, string $tag, string $schema)
    {
        parent::__construct(
            path: "/api/v1/admin/{$path}/{id}",
            summary: 'Update a record. With files, send POST and _method=PUT',
            security: [['sanctum' => []]],
            requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: "#/components/schemas/{$schema}")
            )),
            tags: [$tag],
            parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
            responses: [
                new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(properties: [
                    new OA\Property(property: 'data', ref: "#/components/schemas/{$schema}"),
                ])),
                new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
                new OA\Response(ref: '#/components/responses/NotFound', response: 404),
            ]
        );
    }
}
