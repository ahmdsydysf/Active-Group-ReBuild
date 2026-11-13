<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasImages, HasOrderingScopes, HasTranslations, TouchesSite;

    public const UPLOAD_DIR = 'team';

    public const IMAGES = ['photo'];

    protected $fillable = ['name_ar', 'name_en', 'role_ar', 'role_en', 'photo', 'sort_order', 'is_active'];

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
            'photo' => $this->imageUrl('photo', true),
        ];
    }
}
