<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasImages, HasOrderingScopes, HasTranslations, TouchesSite;

    public const UPLOAD_DIR = 'partners';

    public const IMAGES = ['logo'];

    protected $fillable = ['name_ar', 'name_en', 'field_ar', 'field_en', 'description_ar', 'description_en', 'logo', 'website', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'name' => $this->tr('name', $locale),
            'field' => $this->tr('field', $locale),
            'logo' => $this->imageUrl('logo', true),
            'website' => $this->website,
        ];
    }
}
