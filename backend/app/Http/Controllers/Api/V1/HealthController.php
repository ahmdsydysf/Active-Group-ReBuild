<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

final class HealthController extends Controller
{
    #[OA\Get(
        path: '/api/v1/health',
        summary: 'API health check',
        tags: ['System'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'API is up',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'ok'),
                        new OA\Property(property: 'version', type: 'string', example: 'v1'),
                    ]
                )
            ),
        ]
    )]
    public function __invoke(): JsonResponse
    {
        return response()->json(['status' => 'ok', 'version' => 'v1']);
    }
}
