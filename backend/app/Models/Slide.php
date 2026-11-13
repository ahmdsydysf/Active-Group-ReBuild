<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;

/** Home hero: the first active slide gives the headline; the first three images fill the photo collage. */
class Slide extends Model
{
    use HasImages, HasOrderingScopes, HasTranslations, TouchesSite;

    public const UPLOAD_DIR = 'slides';

    public const IMAGES = ['image'];

    protected $fillable = ['title_ar', 'title_en', 'subtitle_ar', 'subtitle_en', 'button_ar', 'button_en', 'button_url', 'image', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'title' => $this->tr('title', $locale),
            'subtitle' => $this->tr('subtitle', $locale),
            'button' => $this->tr('button', $locale),
            'button_url' => $this->button_url,
            'image' => $this->imageUrl(),
            'image_sm' => $this->imageUrl('image', true),
        ];
    }
}
