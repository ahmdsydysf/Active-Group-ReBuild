<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'team_members';

    /** @var list<string> */
    public array $translatable = ['name', 'position'];

    /** @var list<string> */
    public array $fileFields = ['photo'];

    /** @var list<string> */
    public array $htmlFields = [];

    protected $fillable = [
        'name_ar',
        'name_en',
        'position_ar',
        'position_en',
        'photo',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
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
