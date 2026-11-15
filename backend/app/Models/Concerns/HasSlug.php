<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Fills a unique `slug` from `<slugSource>_en` (or the Arabic value) when it is empty.
 */
trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function (self $model): void {
            if (filled($model->slug)) {
                $model->slug = Str::slug($model->slug, '-', 'ar') ?: $model->slug;
            } else {
                $source = $model->getAttribute($model->slugSource.'_en')
                    ?: $model->getAttribute($model->slugSource.'_ar');
                $model->slug = Str::slug((string) $source, '-', 'ar') ?: Str::lower(Str::random(8));
            }

            $model->slug = Str::limit($model->slug, 200, '');
            $base = $model->slug;
            $i = 2;
            while (static::query()->where('slug', $model->slug)->whereKeyNot($model->getKey())->exists()) {
                $model->slug = "{$base}-{$i}";
                $i++;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
