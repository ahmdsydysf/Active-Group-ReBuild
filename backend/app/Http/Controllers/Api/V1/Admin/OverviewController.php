<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use App\Models\Message;
use App\Models\NewsletterSubscriber;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Slide;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/** Numbers for the dashboard home: KPIs, the content garden, latest messages and products. */
final class OverviewController extends Controller
{
    /** Garden sections: model, translated fields, optional scope. */
    private const SECTIONS = [
        'slides' => [Slide::class, ['title']],
        'products' => [Product::class, ['name']],
        'advice' => [Post::class, ['title'], 'advice'],
        'blog' => [Post::class, ['title'], 'blog'],
        'gallery' => [GalleryPhoto::class, ['caption']],
        'partners' => [Partner::class, ['name']],
        'team' => [TeamMember::class, ['name', 'role']],
        'testimonials' => [Testimonial::class, ['name', 'quote']],
    ];

    #[OA\Get(
        path: '/api/v1/admin/overview',
        summary: 'Dashboard home numbers',
        security: [['sanctum' => []]],
        tags: ['Admin · Overview'],
        responses: [
            new OA\Response(response: 200, description: 'Overview', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'sections', type: 'array', items: new OA\Items(properties: [
                    new OA\Property(property: 'key', type: 'string'),
                    new OA\Property(property: 'total', type: 'integer'),
                    new OA\Property(property: 'missing_en', type: 'integer'),
                ])),
                new OA\Property(property: 'kpis', type: 'object'),
                new OA\Property(property: 'messages_by_month', description: 'Last 12 months, oldest first', type: 'array', items: new OA\Items(type: 'integer')),
                new OA\Property(property: 'latest_messages', type: 'array', items: new OA\Items(ref: '#/components/schemas/MessageAdmin')),
                new OA\Property(property: 'products_by_category', type: 'array', items: new OA\Items(type: 'object')),
                new OA\Property(property: 'latest_products', type: 'array', items: new OA\Items(type: 'object')),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ]
    )]
    public function __invoke(): JsonResponse
    {
        $month = now()->startOfMonth();
        $inbox = Message::query()->where('is_spam', false);

        return response()->json([
            'sections' => array_map(fn (string $key) => $this->section($key, ...self::SECTIONS[$key]), array_keys(self::SECTIONS)),
            'kpis' => [
                'messages_new' => (clone $inbox)->where('status', 'new')->count(),
                'quotes_new' => (clone $inbox)->where('status', 'new')->where('type', 'quote')->count(),
                'products' => Product::query()->where('is_active', true)->count(),
                'products_this_month' => Product::query()->where('created_at', '>=', $month)->count(),
                'subscribers' => NewsletterSubscriber::query()->whereNull('unsubscribed_at')->count(),
                'subscribers_this_month' => NewsletterSubscriber::query()->whereNull('unsubscribed_at')->where('created_at', '>=', $month)->count(),
                'posts' => Post::query()->published()->count(),
                'last_post_at' => Post::query()->published()->value('published_at')?->toIso8601String(),
            ],
            'messages_by_month' => $this->messagesByMonth(),
            'latest_messages' => (clone $inbox)->where('status', '<>', 'archived')->latest()->limit(5)->get(),
            'products_by_category' => ProductCategory::query()->ordered()->withCount('products')->get()
                ->map(fn (ProductCategory $c) => ['name' => $c->name_ar, 'color' => $c->color, 'count' => $c->products_count]),
            'latest_products' => Product::query()->with(['type.category', 'factory'])->latest('updated_at')->limit(5)->get()
                ->map(fn (Product $p) => [
                    'id' => $p->id, 'name_ar' => $p->name_ar, 'name_en' => $p->name_en, 'image_url' => $p->imageUrl('image', true),
                    'category' => $p->type?->category?->name_ar, 'color' => $p->type?->category?->color, 'packs_ar' => $p->packs_ar, 'type' => $p->type?->name_ar, 'factory' => $p->factory?->name_ar, 'is_active' => $p->is_active,
                ]),
        ]);
    }

    /** @param class-string<Model> $model */
    private function section(string $key, string $model, array $fields, ?string $postSection = null): array
    {
        $base = fn () => $model::query()->when($postSection, fn (Builder $q) => $q->where('section', $postSection));

        return [
            'key' => $key,
            'total' => $base()->count(),
            'missing_en' => $base()->where(function (Builder $q) use ($fields): void {
                foreach ($fields as $f) {
                    $q->orWhere(fn ($w) => $w->whereNotNull("{$f}_ar")->where("{$f}_ar", '<>', '')->where(fn ($e) => $e->whereNull("{$f}_en")->orWhere("{$f}_en", '')));
                }
            })->count(),
        ];
    }

    /** Counted in PHP so it runs the same on MySQL and SQLite. */
    private function messagesByMonth(): array
    {
        $start = now()->startOfMonth()->subMonths(11);
        $counts = array_fill(0, 12, 0);
        Message::query()->where('is_spam', false)->where('created_at', '>=', $start)->pluck('created_at')
            ->each(function ($at) use ($start, &$counts): void {
                $i = ($at->year - $start->year) * 12 + $at->month - $start->month;
                if ($i >= 0 && $i < 12) {
                    $counts[$i]++;
                }
            });

        return $counts;
    }
}
