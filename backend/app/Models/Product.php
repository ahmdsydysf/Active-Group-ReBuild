<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes, HasSlug;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'products';

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    /** @var list<string> */
    public array $fileFields = ['image', 'brochure_ar', 'brochure_en'];

    /** @var list<string> */
    public array $htmlFields = ['description_ar', 'description_en'];

    /** Field used to build the slug. */
    public string $slugSource = 'name';

    protected $fillable = [
        'slug',
        'product_type_id',
        'factory_id',
        'partner_id',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image',
        'brochure_ar',
        'brochure_en',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    public function factory(): BelongsTo
    {
        return $this->belongsTo(Factory::class, 'factory_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
