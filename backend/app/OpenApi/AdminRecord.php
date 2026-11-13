<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/** Swagger docs for one ContentController record: show, update and delete on /admin/{path}/{id}. */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class AdminRecord extends OA\PathItem
{
    public function __construct(string $path, string $tag, string $schema)
    {
        $one = new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: "#/components/schemas/{$schema}")]);

        parent::__construct(
            path: "/api/v1/admin/{$path}/{id}",
            get: new OA\Get(
                operationId: 'show-'.$path,
                summary: 'One record',
                security: [['sanctum' => []]],
                tags: [$tag],
                responses: [
                    new OA\Response(response: 200, description: 'Record', content: $one),
                    new OA\Response(ref: '#/components/responses/NotFound', response: 404),
                ],
            ),
            put: new OA\Put(
                operationId: 'update-'.$path,
                summary: 'Update a record. With files, send POST and _method=PUT',
                security: [['sanctum' => []]],
                requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(ref: "#/components/schemas/{$schema}")
                )),
                tags: [$tag],
                responses: [
                    new OA\Response(response: 200, description: 'Updated', content: $one),
                    new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
                    new OA\Response(ref: '#/components/responses/NotFound', response: 404),
                ],
            ),
            delete: new OA\Delete(
                operationId: 'delete-'.$path,
                summary: 'Delete a record and its files',
                security: [['sanctum' => []]],
                tags: [$tag],
                responses: [
                    new OA\Response(response: 204, description: 'Deleted'),
                    new OA\Response(ref: '#/components/responses/NotFound', response: 404),
                ],
            ),
            parameters: [new OA\Parameter(ref: '#/components/parameters/id')],
        );
    }
}
