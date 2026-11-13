<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/** API info and the components shared by every endpoint. */
#[OA\Info(version: '1.0.0', title: 'Active Group API', description: 'REST API for the Active Group website and its dashboard.')]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'API server')]
#[OA\SecurityScheme(securityScheme: 'sanctum', type: 'http', scheme: 'bearer', bearerFormat: 'Token')]
#[OA\Parameter(parameter: 'id', name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
#[OA\Parameter(parameter: 'lang', name: 'lang', in: 'query', required: false, schema: new OA\Schema(type: 'string', default: 'ar', enum: ['ar', 'en']))]
#[OA\Response(response: 'Unauthenticated', description: 'Missing, expired or revoked token')]
#[OA\Response(response: 'NotFound', description: 'No such record')]
#[OA\Response(response: 'ValidationError', description: 'Invalid input', content: new OA\JsonContent(properties: [
    new OA\Property(property: 'message', type: 'string'),
    new OA\Property(property: 'errors', type: 'object', additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string'))),
]))]
final class Spec {}
