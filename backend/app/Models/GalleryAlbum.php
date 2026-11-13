<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use App\Models\Concerns\TouchesSite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryAlbum extends Model
{
    use HasSlug, HasTranslations, TouchesSite;

    public string $slugSource = 'name';

    protected $fillable = ['slug', 'name_ar', 'name_en', 'sort_order'];

    public function photos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }
}
