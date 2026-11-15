<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/** The contact form inbox. */
final class MessageController extends Controller
{
    #[OA\Get(
        path: '/api/v1/admin/messages',
        summary: 'Messages, newest first, with counts per box',
        security: [['sanctum' => []]],
        tags: ['Admin · Messages'],
        parameters: [
            new OA\Parameter(name: 'box', in: 'query', schema: new OA\Schema(type: 'string', enum: ['inbox', 'new', 'archived', 'spam'])),
            new OA\Parameter(name: 'type', in: 'query', schema: new OA\Schema(type: 'string', enum: Message::TYPES)),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Messages', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/MessageAdmin')),
                new OA\Property(property: 'meta', ref: '#/components/schemas/PageMeta'),
                new OA\Property(property: 'counts', properties: [
                    new OA\Property(property: 'inbox', type: 'integer'), new OA\Property(property: 'new', type: 'integer'),
                    new OA\Property(property: 'archived', type: 'integer'), new OA\Property(property: 'spam', type: 'integer'),
                ], type: 'object'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $page = $this->query($request)->latest()->latest('id')->paginate(30);

        return response()->json([
            'data' => $page->items(),
            'meta' => ['total' => $page->total(), 'page' => $page->currentPage(), 'last_page' => $page->lastPage()],
            'counts' => self::counts(),
        ]);
    }

    #[OA\Get(
        path: '/api/v1/admin/messages/{id}',
        summary: 'One message; opening a new message marks it read',
        security: [['sanctum' => []]],
        tags: ['Admin · Messages'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Message', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/MessageAdmin')])),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $message = Message::query()->findOrFail($id);
        if ($message->status === 'new') {
            $message->update(['status' => 'read']);
        }

        return response()->json(['data' => $message]);
    }

    #[OA\Put(
        path: '/api/v1/admin/messages/{id}',
        summary: 'Archive, restore or mark as spam',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: 'status', type: 'string', enum: Message::STATUSES),
            new OA\Property(property: 'is_spam', type: 'boolean'),
        ])),
        tags: ['Admin · Messages'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/MessageAdmin')])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $message = Message::query()->findOrFail($id);
        $message->update($request->validate([
            'status' => ['sometimes', Rule::in(Message::STATUSES)],
            'is_spam' => 'sometimes|boolean',
        ]));

        return response()->json(['data' => $message]);
    }

    #[OA\Post(
        path: '/api/v1/admin/messages/{id}/reply',
        summary: 'Email a reply to the sender and keep a copy on the message',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['body'], properties: [new OA\Property(property: 'body', type: 'string')])),
        tags: ['Admin · Messages'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Sent', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/MessageAdmin')])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function reply(Request $request, int $id): JsonResponse
    {
        $message = Message::query()->findOrFail($id);
        $body = $request->validate(['body' => 'required|string|max:5000'])['body'];

        if (! $message->email) {
            return response()->json(['message' => 'هذه الرسالة بدون بريد إلكتروني، تواصل مع المرسل بالهاتف.', 'errors' => ['body' => ['لا يوجد بريد للرد عليه.']]], 422);
        }

        try {
            Mail::raw($body, fn ($m) => $m->to($message->email, $message->name)->subject(config('app.name').' · رد على رسالتك'));
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'تعذّر إرسال البريد. راجع إعدادات البريد في الاستضافة.'], 502);
        }

        $message->update(['reply' => $body, 'replied_at' => now(), 'status' => 'read']);

        return response()->json(['data' => $message]);
    }

    #[OA\Delete(
        path: '/api/v1/admin/messages/{id}',
        summary: 'Delete a message',
        security: [['sanctum' => []]],
        tags: ['Admin · Messages'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 204, description: 'Deleted')]
    )]
    public function destroy(int $id): JsonResponse
    {
        Message::query()->findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    #[OA\Get(
        path: '/api/v1/admin/messages/export',
        summary: 'Download the current box as CSV',
        security: [['sanctum' => []]],
        tags: ['Admin · Messages'],
        parameters: [new OA\Parameter(name: 'box', in: 'query', schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'CSV file', content: new OA\MediaType(mediaType: 'text/csv'))]
    )]
    public function export(Request $request): StreamedResponse
    {
        return Csv::download('messages', ['التاريخ', 'النوع', 'الاسم', 'الهاتف', 'البريد', 'المحافظة', 'الرسالة', 'الحالة'],
            $this->query($request)->latest()->lazy()->map(fn (Message $m) => [
                $m->created_at?->format('Y-m-d H:i'), $m->type, $m->name, $m->phone, $m->email, $m->governorate, $m->body, $m->status,
            ]));
    }

    /** @return array{inbox: int, new: int, archived: int, spam: int} */
    public static function counts(): array
    {
        $row = Message::query()->selectRaw(
            "sum(case when is_spam = 0 and status <> 'archived' then 1 else 0 end) as inbox,
             sum(case when is_spam = 0 and status = 'new' then 1 else 0 end) as fresh,
             sum(case when is_spam = 0 and status = 'archived' then 1 else 0 end) as archived,
             sum(case when is_spam = 1 then 1 else 0 end) as spam"
        )->first();

        return ['inbox' => (int) $row->inbox, 'new' => (int) $row->fresh, 'archived' => (int) $row->archived, 'spam' => (int) $row->spam];
    }

    private function query(Request $request): Builder
    {
        $query = Message::query();

        match ($request->query('box')) {
            'new' => $query->where('is_spam', false)->where('status', 'new'),
            'archived' => $query->where('is_spam', false)->where('status', 'archived'),
            'spam' => $query->where('is_spam', true),
            default => $query->where('is_spam', false)->where('status', '<>', 'archived'),
        };

        if (in_array($type = $request->query('type'), Message::TYPES, true)) {
            $query->where('type', $type);
        }
        if ($term = trim((string) $request->query('search'))) {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where(fn (Builder $q) => $q->where('name', 'like', $like)->orWhere('phone', 'like', $like)->orWhere('email', 'like', $like)->orWhere('body', 'like', $like));
        }

        return $query;
    }
}
