<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Support\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Throwable;

final class ContactController extends Controller
{
    #[OA\Post(
        path: '/api/v1/contact',
        summary: 'Send a message from the contact page; it lands in the dashboard inbox',
        tags: ['Site'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['type', 'name', 'phone', 'body'],
            properties: [
                new OA\Property(property: 'type', type: 'string', enum: Message::TYPES),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'phone', type: 'string'),
                new OA\Property(property: 'email', type: 'string', format: 'email'),
                new OA\Property(property: 'governorate', type: 'string'),
                new OA\Property(property: 'body', type: 'string'),
                new OA\Property(property: 'locale', type: 'string', enum: ['ar', 'en']),
                new OA\Property(property: 'website', description: 'Leave empty. Bots fill it and their message is dropped', type: 'string'),
            ]
        )),
        responses: [
            new OA\Response(response: 201, description: 'Received'),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
            new OA\Response(response: 429, description: 'Too many requests'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(Message::TYPES)],
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9 ]{7,20}$/'],
            'email' => 'nullable|email:rfc|max:120',
            'governorate' => 'nullable|string|max:60',
            'body' => 'required|string|min:5|max:3000',
            'locale' => 'nullable|in:ar,en',
            'website' => 'nullable|string|max:200',
        ]);

        // Honeypot: answer as if it worked, keep nothing.
        if (filled($data['website'] ?? null)) {
            return response()->json(['ok' => true], 201);
        }
        unset($data['website']);

        $message = Message::query()->create([...$data, 'locale' => $data['locale'] ?? 'ar', 'status' => 'new', 'ip' => $request->ip()]);

        // The message is saved either way; a mail failure only skips the alert.
        if ($to = Settings::all()['inbox_email']) {
            try {
                Mail::raw(
                    "{$message->name} · {$message->phone}".($message->email ? " · {$message->email}" : '')."\n\n{$message->body}",
                    fn ($m) => $m->to($to)->subject('رسالة جديدة من الموقع: '.__("messages.type.{$message->type}"))->replyTo($message->email ?: $to),
                );
            } catch (Throwable $e) {
                report($e);
            }
        }

        return response()->json(['ok' => true], 201);
    }
}
