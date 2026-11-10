<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentVersion;
use App\Models\Concerns\HasFiles;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

/**
 * Single-row table with company profile, contact details and social links.
 */
class SiteSetting extends Model
{
    use BumpsContentVersion, HasFiles, HasTranslations;

    public const UPLOAD_DIR = 'settings';

    public const TRANSLATABLE = ['site_name', 'slogan', 'overview', 'mission', 'vision', 'address', 'address2', 'seo_description'];

    public const PLAIN = ['video_url', 'phones', 'phones2', 'email', 'email2', 'whatsapp', 'fax', 'map_url', 'map_url2', 'facebook', 'twitter', 'linkedin', 'instagram', 'youtube'];

    /** @var list<string> */
    public array $fileFields = ['logo', 'about_image'];

    /** @var list<string> */
    public array $htmlFields = ['overview_ar', 'overview_en', 'mission_ar', 'mission_en', 'vision_ar', 'vision_en'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1], [
            'site_name_ar' => 'مجموعة أكتيف',
            'site_name_en' => 'Active Group',
        ]);
    }
}
