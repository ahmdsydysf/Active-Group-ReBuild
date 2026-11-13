<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    use HasSlug, HasTranslations;

    public string $slugSource = 'name';

    protected $fillable = ['slug', 'name_ar', 'name_en', 'sort_order'];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
