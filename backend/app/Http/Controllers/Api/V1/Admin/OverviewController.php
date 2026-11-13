<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use App\Models\Partner;
use App\Models\Slide;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/** Numbers for the dashboard home: section sizes and how many items still miss English. */
final class OverviewController extends Controller
{
    /** Sections shown in the "content garden"; each later page adds its own. */
    private const SECTIONS = [
        'slides' => [Slide::class, ['title']],
        'testimonials' => [Testimonial::class, ['name', 'quote']],
        'partners' => [Partner::class, ['name']],
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
                new OA\Property(property: 'subscribers', properties: [
                    new OA\Property(property: 'total', type: 'integer'),
                    new OA\Property(property: 'this_month', type: 'integer'),
                ], type: 'object'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ]
    )]
    public function __invoke(): JsonResponse
    {
        $sections = [];
        foreach (self::SECTIONS as $key => [$model, $fields]) {
            /** @var class-string<Model> $model */
            $sections[] = [
                'key' => $key,
                'total' => $model::query()->count(),
                'missing_en' => $model::query()->where(function ($q) use ($fields): void {
                    foreach ($fields as $f) {
                        $q->orWhere(fn ($w) => $w->whereNotNull("{$f}_ar")->where(fn ($e) => $e->whereNull("{$f}_en")->orWhere("{$f}_en", '')));
                    }
                })->count(),
            ];
        }

        return response()->json([
            'sections' => $sections,
            'subscribers' => [
                'total' => NewsletterSubscriber::query()->count(),
                'this_month' => NewsletterSubscriber::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            ],
        ]);
    }
}
