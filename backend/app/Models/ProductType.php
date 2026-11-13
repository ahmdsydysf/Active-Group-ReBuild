<?php

namespace App\Models;

use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductType extends Model
{
    use HasOrderingScopes, HasSlug, HasTranslations, TouchesSite;

    public string $slugSource = 'name';

    protected $fillable = ['product_category_id', 'slug', 'name_ar', 'name_en', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
