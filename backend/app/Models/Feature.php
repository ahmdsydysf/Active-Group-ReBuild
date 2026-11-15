<?php

namespace App\Models;

use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;

/** A "why choose us" tile on the about page. */
class Feature extends Model
{
    use HasOrderingScopes, HasTranslations, TouchesSite;

    public const ICONS = ['shield', 'people', 'truck', 'lab'];

    public const COLORS = ['field', 'bloom', 'sky', 'wheat', 'rust', 'deep'];

    protected $fillable = ['title_ar', 'title_en', 'description_ar', 'description_en', 'icon', 'color', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'title' => $this->tr('title', $locale),
            'description' => $this->tr('description', $locale),
            'icon' => $this->icon,
            'color' => $this->color,
        ];
    }
}
