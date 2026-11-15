<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryPhoto extends Model
{
    use HasImages, HasOrderingScopes, HasTranslations, TouchesSite;

    public const UPLOAD_DIR = 'gallery';

    public const IMAGES = ['image'];

    protected $fillable = ['gallery_album_id', 'image', 'caption_ar', 'caption_en', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id');
    }

    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'album' => $this->album?->slug,
            'caption' => $this->tr('caption', $locale),
            'image' => $this->imageUrl(),
            'image_sm' => $this->imageUrl('image', true),
        ];
    }
}
