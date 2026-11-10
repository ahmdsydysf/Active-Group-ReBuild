<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasOrderingScopes;
use App\Models\Concerns\HasSlug;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasTranslations, HasFiles, BumpsContentVersion, HasOrderingScopes, HasSlug;

    /** Folder on the public disk where this model's uploads live. */
    public const UPLOAD_DIR = 'posts';

    /** @var list<string> */
    public array $translatable = ['title', 'excerpt', 'body'];

    /** @var list<string> */
    public array $fileFields = ['cover'];

    /** @var list<string> */
    public array $htmlFields = ['body_ar', 'body_en'];

    /** Field used to build the slug. */
    public string $slugSource = 'title';

    protected $fillable = [
        'slug',
        'title_ar',
        'title_en',
        'excerpt_ar',
        'excerpt_en',
        'body_ar',
        'body_en',
        'cover',
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
}
