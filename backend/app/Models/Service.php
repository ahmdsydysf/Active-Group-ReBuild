<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes, HasSlug;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'services';

    /** @var list<string> */
    public array $translatable = ['title', 'body'];

    /** @var list<string> */
    public array $fileFields = ['image', 'brochure_ar', 'brochure_en'];

    /** @var list<string> */
    public array $htmlFields = ['body_ar', 'body_en'];

    /** Field used to build the slug. */
    public string $slugSource = 'title';

    protected $fillable = [
        'slug',
        'service_type_id',
        'title_ar',
        'title_en',
        'body_ar',
        'body_en',
        'image',
        'video_url',
        'brochure_ar',
        'brochure_en',
        'published_at',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id');
    }
}
