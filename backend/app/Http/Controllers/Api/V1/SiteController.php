<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Feature;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\Slide;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\Settings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

/** Read-only content for the public site. Localized with `?lang=ar|en` (Arabic by default). */
final class SiteController extends Controller
{
    #[OA\Get(
        path: '/api/v1/site',
        summary: 'Shared site data: contact details, social links, numbers, SEO, footer categories',
        tags: ['Site'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/lang')],
        responses: [new OA\Response(response: 200, description: 'Site data', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Site'),
        ]))]
    )]
    public function site(Request $request): JsonResponse
    {
        $l = $this->locale($request);

        return $this->cached(['data' => [
            ...Settings::forSite($l),
            // Footer product links on every page.
            'categories' => ProductCategory::query()->active()->ordered()->get(['slug', 'name_ar', 'name_en'])
                ->map(fn (ProductCategory $c) => ['slug' => $c->slug, 'name' => $c->tr('name', $l)]),
        ]]);
    }

    #[OA\Get(
        path: '/api/v1/home',
        summary: 'Everything the home page shows, in one request',
        tags: ['Site'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/lang')],
        responses: [new OA\Response(response: 200, description: 'Home content', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Home'),
        ]))]
    )]
    public function home(Request $request): JsonResponse
    {
        $l = $this->locale($request);

        return $this->cached(['data' => [
            'slides' => Slide::query()->active()->ordered()->limit(3)->get()->map->toPublic($l),
            'categories' => ProductCategory::query()->active()->ordered()
                ->with(['types' => fn ($q) => $q->active()->ordered()->limit(3)])
                ->withCount(['products' => fn (Builder $q) => $q->where('products.is_active', true)])
                ->get()->map->toPublic($l),
            'advice' => Post::query()->published()->where('section', 'advice')->with('category')->limit(5)->get()->map->toCard($l),
            'partners' => Partner::query()->active()->ordered()->get()->map->toPublic($l),
            'testimonials' => Testimonial::query()->active()->ordered()->limit(5)->get()->map->toPublic($l),
        ]]);
    }

    #[OA\Get(
        path: '/api/v1/catalog',
        summary: 'Every active product with its full details, plus categories and factories (the static site builds all product pages from this)',
        tags: ['Site'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/lang')],
        responses: [new OA\Response(response: 200, description: 'Catalog', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Catalog'),
        ]))]
    )]
    public function catalog(Request $request): JsonResponse
    {
        $l = $this->locale($request);
        $types = fn ($q) => $q->active()->ordered();

        return $this->cached(['data' => [
            'categories' => ProductCategory::query()->active()->ordered()->with(['types' => $types])
                ->withCount(['products' => fn (Builder $q) => $q->where('products.is_active', true)])
                ->get()->map->toPublic($l),
            'factories' => Factory::query()->active()->ordered()->get()->map->toPublic($l),
            'products' => Product::query()->active()
                ->whereHas('type', fn (Builder $q) => $q->active()->whereHas('category', fn (Builder $c) => $c->active()))
                ->with(['type.category', 'factory'])
                ->orderByDesc('is_featured')->ordered()->get()->map->toPublic($l),
        ]]);
    }

    #[OA\Get(
        path: '/api/v1/posts',
        summary: 'Published articles of one section with their bodies, plus the section categories',
        tags: ['Site'],
        parameters: [
            new OA\Parameter(ref: '#/components/parameters/lang'),
            new OA\Parameter(name: 'section', in: 'query', required: true, schema: new OA\Schema(type: 'string', enum: Post::SECTIONS)),
        ],
        responses: [new OA\Response(response: 200, description: 'Articles', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', properties: [
                new OA\Property(property: 'categories', type: 'array', items: new OA\Items(properties: [
                    new OA\Property(property: 'slug', type: 'string'), new OA\Property(property: 'name', type: 'string'),
                ])),
                new OA\Property(property: 'posts', type: 'array', items: new OA\Items(ref: '#/components/schemas/Post')),
            ], type: 'object'),
        ]))]
    )]
    public function posts(Request $request): JsonResponse
    {
        $l = $this->locale($request);
        $section = $request->validate(['section' => ['required', Rule::in(Post::SECTIONS)]])['section'];

        return $this->cached(['data' => [
            'categories' => PostCategory::query()->where('section', $section)->ordered()
                ->whereHas('posts', fn (Builder $q) => $q->published())
                ->get()->map(fn (PostCategory $c) => ['slug' => $c->slug, 'name' => $c->tr('name', $l)]),
            'posts' => Post::query()->published()->where('section', $section)->with(['category', 'author'])->get()->map->toPublic($l),
        ]]);
    }

    #[OA\Get(
        path: '/api/v1/about',
        summary: 'The about page: story, reasons to choose us, timeline, factories, team and testimonials',
        tags: ['Site'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/lang')],
        responses: [new OA\Response(response: 200, description: 'About', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/About'),
        ]))]
    )]
    public function about(Request $request): JsonResponse
    {
        $l = $this->locale($request);

        return $this->cached(['data' => [
            ...Settings::about($l),
            'features' => Feature::query()->active()->ordered()->get()->map->toPublic($l),
            'factories' => Factory::query()->active()->ordered()->get()->map->toPublic($l),
            'team' => TeamMember::query()->active()->ordered()->get()->map->toPublic($l),
            'testimonials' => Testimonial::query()->active()->ordered()->get()->map->toPublic($l),
        ]]);
    }

    #[OA\Get(
        path: '/api/v1/pages',
        summary: 'Short lists for the partners, services and gallery pages',
        tags: ['Site'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/lang')],
        responses: [new OA\Response(response: 200, description: 'Pages', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Pages'),
        ]))]
    )]
    public function pages(Request $request): JsonResponse
    {
        $l = $this->locale($request);

        return $this->cached(['data' => [
            'partners' => Partner::query()->active()->ordered()->get()->map->toPublic($l),
            'services' => Service::query()->active()->ordered()->get()->map->toPublic($l),
            'albums' => GalleryAlbum::query()->ordered()->whereHas('photos', fn (Builder $q) => $q->where('is_active', true))
                ->get()->map(fn (GalleryAlbum $a) => ['slug' => $a->slug, 'name' => $a->tr('name', $l)]),
            'photos' => GalleryPhoto::query()->active()->ordered()->with('album')->get()->map->toPublic($l),
        ]]);
    }

    private function locale(Request $request): string
    {
        return $request->query('lang') === 'en' ? 'en' : 'ar';
    }

    private function cached(array $body): JsonResponse
    {
        return response()->json($body)->header('Cache-Control', 'public, max-age=60');
    }
}
