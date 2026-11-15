<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Support\Csv;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** The newsletter list (القائمة البريدية). */
final class SubscriberController extends Controller
{
    #[OA\Get(
        path: '/api/v1/admin/subscribers',
        summary: 'Subscribers, newest first, with totals',
        security: [['sanctum' => []]],
        tags: ['Admin · Newsletter'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['active', 'unsubscribed'])),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Subscribers', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SubscriberAdmin')),
                new OA\Property(property: 'meta', ref: '#/components/schemas/PageMeta'),
                new OA\Property(property: 'stats', properties: [
                    new OA\Property(property: 'active', type: 'integer'),
                    new OA\Property(property: 'this_month', type: 'integer'),
                    new OA\Property(property: 'unsubscribed', type: 'integer'),
                ], type: 'object'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $page = $this->query($request)->latest()->latest('id')->paginate(50);

        return response()->json([
            'data' => $page->items(),
            'meta' => ['total' => $page->total(), 'page' => $page->currentPage(), 'last_page' => $page->lastPage()],
            'stats' => [
                'active' => NewsletterSubscriber::query()->whereNull('unsubscribed_at')->count(),
                'this_month' => NewsletterSubscriber::query()->whereNull('unsubscribed_at')->where('created_at', '>=', now()->startOfMonth())->count(),
                'unsubscribed' => NewsletterSubscriber::query()->whereNotNull('unsubscribed_at')->count(),
            ],
        ]);
    }

    #[OA\Put(
        path: '/api/v1/admin/subscribers/{id}',
        summary: 'Unsubscribe or re-subscribe',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(properties: [new OA\Property(property: 'subscribed', type: 'boolean')])),
        tags: ['Admin · Newsletter'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/SubscriberAdmin')]))]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $subscriber = NewsletterSubscriber::query()->findOrFail($id);
        $subscribed = $request->validate(['subscribed' => 'required|boolean'])['subscribed'];
        $subscriber->update(['unsubscribed_at' => $subscribed ? null : now()]);

        return response()->json(['data' => $subscriber]);
    }

    #[OA\Delete(
        path: '/api/v1/admin/subscribers/{id}',
        summary: 'Remove an email from the list for good',
        security: [['sanctum' => []]],
        tags: ['Admin · Newsletter'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 204, description: 'Deleted')]
    )]
    public function destroy(int $id): JsonResponse
    {
        NewsletterSubscriber::query()->findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    #[OA\Get(
        path: '/api/v1/admin/subscribers/export',
        summary: 'Download the list as CSV',
        security: [['sanctum' => []]],
        tags: ['Admin · Newsletter'],
        parameters: [new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'CSV file', content: new OA\MediaType(mediaType: 'text/csv'))]
    )]
    public function export(Request $request): StreamedResponse
    {
        return Csv::download('subscribers', ['email', 'locale', 'source', 'subscribed_at', 'unsubscribed_at'],
            $this->query($request)->latest()->lazy()->map(fn (NewsletterSubscriber $s) => [
                $s->email, $s->locale, $s->source, $s->created_at?->format('Y-m-d'), $s->unsubscribed_at?->format('Y-m-d'),
            ]));
    }

    private function query(Request $request): Builder
    {
        return NewsletterSubscriber::query()
            ->when($request->query('status') === 'active', fn ($q) => $q->whereNull('unsubscribed_at'))
            ->when($request->query('status') === 'unsubscribed', fn ($q) => $q->whereNotNull('unsubscribed_at'))
            ->when(trim((string) $request->query('search')), fn ($q, $t) => $q->where('email', 'like', '%'.addcslashes($t, '%_\\').'%'));
    }
}
