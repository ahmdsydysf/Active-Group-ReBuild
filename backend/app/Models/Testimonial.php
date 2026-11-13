<?php

namespace App\Models;

use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasOrderingScopes, HasTranslations, TouchesSite;

    /** Card colours the dashboard offers; they map to the site palette. */
    public const COLORS = ['field', 'rust', 'sky', 'plum', 'deep', 'wheat', 'bloom'];

    protected $fillable = ['name_ar', 'name_en', 'role_ar', 'role_en', 'quote_ar', 'quote_en', 'color', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'name' => $this->tr('name', $locale),
            'role' => $this->tr('role', $locale),
            'quote' => $this->tr('quote', $locale),
            'color' => $this->color,
        ];
    }
}
