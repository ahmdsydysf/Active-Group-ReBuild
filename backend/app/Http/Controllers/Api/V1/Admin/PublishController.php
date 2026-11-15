<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Support\Publisher;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use RuntimeException;

/** The "نشر التغييرات" button: rebuilds the static site from the current content. */
final class PublishController extends Controller
{
    #[OA\Get(
        path: '/api/v1/admin/publish',
        summary: 'Whether content changed since the last publish',
        security: [['sanctum' => []]],
        tags: ['Admin · Publish'],
        responses: [new OA\Response(response: 200, description: 'Status', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/PublishStatus')]))]
    )]
    public function show(): JsonResponse
    {
        return response()->json(['data' => Publisher::status()]);
    }

    #[OA\Post(
        path: '/api/v1/admin/publish',
        summary: 'Start a rebuild and upload of the public site (takes a few minutes)',
        security: [['sanctum' => []]],
        tags: ['Admin · Publish'],
        responses: [
            new OA\Response(response: 202, description: 'Started', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/PublishStatus')])),
            new OA\Response(response: 503, description: 'Publishing is not configured or GitHub refused'),
        ]
    )]
    public function store(): JsonResponse
    {
        try {
            Publisher::publish();
        } catch (RuntimeException $e) {
            report($e);

            return response()->json(['message' => Publisher::configured()
                ? 'تعذّر بدء النشر الآن. حاول بعد دقيقة.'
                : 'النشر غير مُعد بعد على السيرفر. راجع دليل التشغيل.'], 503);
        }

        return response()->json(['data' => Publisher::status()], 202);
    }
}
