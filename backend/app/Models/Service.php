<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A technical tip or consultation article. */
class Service extends Model
{
    use HasImages, HasSlug, HasTranslations;

    public const UPLOAD_DIR = 'services';

    public const IMAGES = ['image'];

    /** Arabic and English readers both average about 200 words a minute. */
    private const WORDS_PER_MINUTE = 200;

    public string $slugSource = 'title';

    protected $fillable = ['service_type_id', 'slug', 'title_ar', 'title_en', 'excerpt_ar', 'excerpt_en', 'body_ar', 'body_en', 'image', 'published_at', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'published_at' => 'datetime'];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderByDesc('published_at')->orderByDesc('id');
    }

    public function readMinutes(string $locale): int
    {
        $text = trim(strip_tags((string) $this->tr('body', $locale)));
        $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text) ?: []);

        return max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
    }

    /** Expects `type` loaded. */
    public function toPublic(string $locale): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->tr('title', $locale),
            'excerpt' => $this->tr('excerpt', $locale),
            'type' => $this->type?->tr('name', $locale),
            'image' => $this->imageUrl(),
            'image_sm' => $this->imageUrl('image', true),
            'read_minutes' => $this->readMinutes($locale),
        ];
    }
}
