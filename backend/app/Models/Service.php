<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;

/** A technical service card on the services page. */
class Service extends Model
{
    use HasImages, HasOrderingScopes, HasTranslations, TouchesSite;

    public const UPLOAD_DIR = 'services';

    public const IMAGES = ['image'];

    /** Icons the site can draw on a service card. */
    public const ICONS = ['lab', 'leaf', 'shield', 'visit', 'training', 'truck'];

    protected $fillable = ['title_ar', 'title_en', 'summary_ar', 'summary_en', 'icon', 'image', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'title' => $this->tr('title', $locale),
            'summary' => $this->tr('summary', $locale),
            'icon' => $this->icon,
            'image' => $this->imageUrl('image', true),
        ];
    }
}
