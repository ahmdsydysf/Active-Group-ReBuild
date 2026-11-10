<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'gallery_items';

    /** @var list<string> */
    public array $translatable = ['caption'];

    /** @var list<string> */
    public array $fileFields = ['media'];

    /** @var list<string> */
    public array $htmlFields = [];

    protected $fillable = [
        'gallery_category_id',
        'type',
        'media',
        'video_url',
        'caption_ar',
        'caption_en',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(GalleryCategory::class, 'gallery_category_id');
    }
}
