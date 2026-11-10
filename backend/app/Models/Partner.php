<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'partners';

    /** @var list<string> */
    public array $translatable = ['name', 'description'];

    /** @var list<string> */
    public array $fileFields = ['logo'];

    /** @var list<string> */
    public array $htmlFields = [];

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'logo',
        'website',
        'facebook',
        'twitter',
        'instagram',
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
