<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use App\Support\Html;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasImages, HasOrderingScopes, HasSlug, HasTranslations, TouchesSite;

    public const UPLOAD_DIR = 'products';

    public const IMAGES = ['image'];

    /** Non-image uploads: column => [allowed extensions, max KB]. */
    public const FILES = ['leaflet' => ['pdf', 10240]];

    public string $slugSource = 'name';

    protected $fillable = [
        'product_type_id', 'factory_id', 'slug', 'name_ar', 'name_en', 'active_ingredient_ar', 'active_ingredient_en',
        'formulation_ar', 'formulation_en', 'summary_ar', 'summary_en', 'body_ar', 'body_en', 'packs_ar', 'packs_en',
        'registration_no', 'doses_ar', 'doses_en', 'faqs_ar', 'faqs_en', 'safety_ar', 'safety_en', 'image', 'leaflet',
        'seo_title_ar', 'seo_title_en', 'seo_description_ar', 'seo_description_en', 'in_stock', 'is_featured', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean', 'is_featured' => 'boolean', 'in_stock' => 'boolean', 'sort_order' => 'integer',
            'doses_ar' => 'array', 'doses_en' => 'array', 'faqs_ar' => 'array', 'faqs_en' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(fn (self $p) => $p->leaflet && Storage::disk('public')->delete($p->leaflet));
    }

    protected function bodyAr(): Attribute
    {
        return Attribute::set(fn (?string $v) => Html::clean($v));
    }

    protected function bodyEn(): Attribute
    {
        return Attribute::set(fn (?string $v) => Html::clean($v));
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }

    /** List rows in the requested language, or the other one when a row has none. */
    private function rows(string $field, string $locale): array
    {
        $rows = $this->getAttribute("{$field}_{$locale}") ?: $this->getAttribute("{$field}_".($locale === 'ar' ? 'en' : 'ar'));

        return array_values($rows ?? []);
    }

    /** Card data for the products grid. Expects `type.category` loaded. */
    public function toCard(string $locale): array
    {
        $other = $locale === 'ar' ? 'en' : 'ar';
        $packs = (string) $this->tr('packs', $locale);

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->tr('name', $locale),
            // Shown small under the name: the English name on Arabic pages and the other way round.
            'name_alt' => $this->getAttribute("name_{$other}") ?: null,
            'active_ingredient' => $this->tr('active_ingredient', $locale),
            'category' => $this->type->category->slug,
            'type' => $this->type->slug,
            'type_name' => $this->type->tr('name', $locale),
            'category_name' => $this->type->category->tr('name', $locale),
            'color' => $this->type->category->color,
            'pack' => trim(preg_split('/[·,،]/u', $packs)[0] ?? '') ?: null,
            'crops' => array_values(array_unique(array_filter(array_map(fn ($r) => trim($r['crop'] ?? ''), $this->rows('doses', $locale))))),
            'image' => $this->imageUrl('image', true),
            'is_featured' => $this->is_featured,
        ];
    }

    /** Everything the product page shows. Expects `type.category` and `factory` loaded. */
    public function toPublic(string $locale): array
    {
        return [
            ...$this->toCard($locale),
            'image_lg' => $this->imageUrl(),
            'summary' => $this->tr('summary', $locale),
            'body' => $this->tr('body', $locale),
            'formulation' => $this->tr('formulation', $locale),
            'packs' => $this->tr('packs', $locale),
            'registration_no' => $this->registration_no,
            'in_stock' => $this->in_stock,
            'doses' => $this->rows('doses', $locale),
            'faqs' => $this->rows('faqs', $locale),
            'safety' => array_values(array_filter(array_map('trim', explode("\n", (string) $this->tr('safety', $locale))))),
            'leaflet' => $this->leaflet ? Storage::disk('public')->url($this->leaflet) : null,
            'factory' => $this->factory?->tr('name', $locale),
            'seo' => ['title' => $this->tr('seo_title', $locale), 'description' => $this->tr('seo_description', $locale) ?: $this->tr('summary', $locale)],
        ];
    }
}
