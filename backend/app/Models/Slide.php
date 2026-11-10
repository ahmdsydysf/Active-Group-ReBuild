<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'slides';

    /** @var list<string> */
    public array $translatable = ['title', 'subtitle'];

    /** @var list<string> */
    public array $fileFields = ['image'];

    /** @var list<string> */
    public array $htmlFields = [];

    protected $fillable = [
        'title_ar',
        'title_en',
        'subtitle_ar',
        'subtitle_en',
        'image',
        'link_url',
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
}
