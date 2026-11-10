<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'features';

    /** @var list<string> */
    public array $translatable = ['title', 'description'];

    /** @var list<string> */
    public array $fileFields = ['icon'];

    /** @var list<string> */
    public array $htmlFields = [];

    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'icon',
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
