<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factory extends Model
{
    use HasImages, HasOrderingScopes, HasTranslations, TouchesSite;

    public const UPLOAD_DIR = 'factories';

    public const IMAGES = ['image'];

    protected $fillable = ['name_ar', 'name_en', 'description_ar', 'description_en', 'address_ar', 'address_en', 'image', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'name' => $this->tr('name', $locale),
            'description' => $this->tr('description', $locale),
            'address' => $this->tr('address', $locale),
            'image_sm' => $this->imageUrl('image', true),
        ];
    }
}
