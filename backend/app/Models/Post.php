<?php

namespace App\Models;

use App\Models\Concerns\HasImages;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use App\Support\Html;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An article. `section` is "advice" (الخبرات والنصائح) or "blog" (المدونة).
 * Status: draft when inactive, scheduled when active with a future date, otherwise published.
 */
class Post extends Model
{
    use HasImages, HasSlug, HasTranslations, TouchesSite;

    public const SECTIONS = ['advice', 'blog'];

    public const UPLOAD_DIR = 'posts';

    public const IMAGES = ['image'];

    /** Arabic and English readers both average about 200 words a minute. */
    private const WORDS_PER_MINUTE = 200;

    public string $slugSource = 'title';

    protected $fillable = [
        'section', 'post_category_id', 'author_id', 'slug', 'title_ar', 'title_en', 'excerpt_ar', 'excerpt_en', 'body_ar', 'body_en',
        'image', 'seo_description_ar', 'seo_description_en', 'is_active', 'is_featured', 'published_at',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_featured' => 'boolean', 'published_at' => 'datetime'];
    }

    protected function bodyAr(): Attribute
    {
        return Attribute::set(fn (?string $v) => Html::clean($v));
    }

    protected function bodyEn(): Attribute
    {
        return Attribute::set(fn (?string $v) => Html::clean($v));
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'author_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderByDesc('published_at')->orderByDesc('id');
    }

    public function status(): string
    {
        return match (true) {
            ! $this->is_active => 'draft',
            $this->published_at?->isFuture() ?? false => 'scheduled',
            default => 'published',
        };
    }

    public function readMinutes(string $locale): int
    {
        $text = trim(strip_tags((string) $this->tr('body', $locale)));
        $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text) ?: []);

        return max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
    }

    /** Card data. Expects `category` loaded. */
    public function toCard(string $locale): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'section' => $this->section,
            'title' => $this->tr('title', $locale),
            'excerpt' => $this->tr('excerpt', $locale),
            'category' => $this->category?->slug,
            'type' => $this->category?->tr('name', $locale),
            'image' => $this->imageUrl(),
            'image_sm' => $this->imageUrl('image', true),
            'read_minutes' => $this->readMinutes($locale),
            'published_at' => ($this->published_at ?? $this->created_at)?->toDateString(),
            'is_featured' => $this->is_featured,
        ];
    }

    /** The full article. Expects `category` and `author` loaded. */
    public function toPublic(string $locale): array
    {
        return [
            ...$this->toCard($locale),
            'body' => $this->tr('body', $locale),
            'author' => $this->author ? ['name' => $this->author->tr('name', $locale), 'photo' => $this->author->imageUrl('photo', true)] : null,
            'seo_description' => $this->tr('seo_description', $locale) ?: $this->tr('excerpt', $locale),
        ];
    }
}
