<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\Slide;
use App\Models\Testimonial;
use App\Support\Settings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/** Read-only content for the public site. Localized with `?lang=ar|en` (Arabic by default). */
final class SiteController extends Controller
{
    #[OA\Get(
        path: '/api/v1/site',
        summary: 'Shared site data: contact details, social links, numbers, SEO',
        tags: ['Site'],
        parameters: [new OA\Parameter(ref: '#/components/parameters/lang')],
        responses: [new OA\Response(response: 200, description: 'Site data', content: new OA\JsonContent(properties: [
            new OA\Property(property: 'data', ref: '#/components/schemas/Site'),
        ]))]
    )]
    public function site(Request $request): JsonResponse
    {
        return $this->cached(['data' => Settings::forSite($this->locale($request))]);
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
            'services' => Service::query()->published()->with('type')->limit(5)->get()->map->toPublic($l),
            'partners' => Partner::query()->active()->ordered()->get()->map->toPublic($l),
            'testimonials' => Testimonial::query()->active()->ordered()->limit(5)->get()->map->toPublic($l),
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
