<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

final class NewsletterController extends Controller
{
    #[OA\Post(
        path: '/api/v1/newsletter',
        summary: 'Subscribe an email to the weekly tips newsletter',
        tags: ['Site'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['email'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email'),
                new OA\Property(property: 'locale', type: 'string', enum: ['ar', 'en']),
            ]
        )),
        responses: [
            new OA\Response(response: 201, description: 'Subscribed (also returned when the email was already on the list)'),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
            new OA\Response(response: 429, description: 'Too many requests'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email:rfc|max:120',
            'locale' => 'nullable|in:ar,en',
        ]);

        // The same answer either way, so the form cannot be used to check who is subscribed.
        NewsletterSubscriber::query()->firstOrCreate(
            ['email' => mb_strtolower($data['email'])],
            ['locale' => $data['locale'] ?? 'ar'],
        );

        return response()->json(['ok' => true], 201);
    }
}
