<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Active Group API',
    description: 'REST API for the Active Group website and admin dashboard.'
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'API server')]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token'
)]
final class OpenApi
{
}
