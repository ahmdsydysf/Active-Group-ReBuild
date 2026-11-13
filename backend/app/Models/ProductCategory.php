<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ProductCategory extends Model
{
    use HasImages, HasOrderingScopes, HasSlug, HasTranslations;

    public const UPLOAD_DIR = 'categories';

    public const IMAGES = ['image'];

    public string $slugSource = 'name';

    protected $fillable = ['slug', 'name_ar', 'name_en', 'description_ar', 'description_en', 'color', 'image', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function types(): HasMany
    {
        return $this->hasMany(ProductType::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, ProductType::class);
    }

    /** Expects `types` loaded and `products_count` counted. */
    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->tr('name', $locale),
            'description' => $this->tr('description', $locale),
            'color' => $this->color,
            'products_count' => (int) $this->products_count,
            'types' => $this->types->map(fn (ProductType $t) => $t->tr('name', $locale))->values(),
        ];
    }
}
