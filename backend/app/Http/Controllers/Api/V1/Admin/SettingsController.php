<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Support\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class SettingsController extends Controller
{
    #[OA\Get(
        path: '/api/v1/admin/settings',
        summary: 'All site settings, both languages',
        security: [['sanctum' => []]],
        tags: ['Admin · Settings'],
        responses: [
            new OA\Response(response: 200, description: 'Settings', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/SettingsAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ]
    )]
    public function show(): JsonResponse
    {
        return response()->json(['data' => Settings::all()]);
    }

    #[OA\Put(
        path: '/api/v1/admin/settings',
        summary: 'Update some or all settings',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/SettingsAdmin')),
        tags: ['Admin · Settings'],
        responses: [
            new OA\Response(response: 200, description: 'Saved settings', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/SettingsAdmin'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function update(Request $request): JsonResponse
    {
        Settings::update($request->validate(Settings::rules()));

        return response()->json(['data' => Settings::all()]);
    }
}
