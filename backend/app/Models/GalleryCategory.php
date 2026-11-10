<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GalleryCategory extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes, HasSlug;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'gallery_categories';

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    /** @var list<string> */
    public array $fileFields = [];

    /** @var list<string> */
    public array $htmlFields = [];

    /** Field used to build the slug. */
    public string $slugSource = 'name';

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class, 'gallery_category_id');
    }
}
