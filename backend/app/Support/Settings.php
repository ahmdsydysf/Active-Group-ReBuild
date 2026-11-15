<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Site-wide settings edited from the dashboard "إعدادات الموقع" page.
 * DEFAULTS lists every key the site knows; anything else is ignored.
 */
final class Settings
{
    private const CACHE_KEY = 'settings.all';

    public const DEFAULTS = [
        // General
        'site_name_ar' => 'أكتيف جروب',
        'site_name_en' => 'Active Group',
        'footer_about_ar' => null,
        'footer_about_en' => null,
        // Contact
        'phone1' => null,
        'phone2' => null,
        'whatsapp' => null,
        'email' => null,
        'inbox_email' => null,
        'address_ar' => null,
        'address_en' => null,
        'hours_ar' => null,
        'hours_en' => null,
        'map_url' => null,
        // Social
        'facebook' => null,
        'youtube' => null,
        'linkedin' => null,
        'instagram' => null,
        // Numbers: four counters on the home page. The third one is also the badge on the hero photos.
        'stats' => [],
        // About page
        'about_title_ar' => null,
        'about_title_en' => null,
        'about_body_ar' => null,
        'about_body_en' => null,
        // Milestones on the about page: [{year, title_ar, title_en, text_ar, text_en}]
        'timeline' => [],
        // SEO
        'seo_title_ar' => null,
        'seo_title_en' => null,
        'seo_description_ar' => null,
        'seo_description_en' => null,
    ];

    public static function rules(): array
    {
        $text = 'nullable|string|max:255';
        $url = 'nullable|url:https,http|max:500';
        $phone = ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9 ]{6,20}$/'];
        $stat = fn (string $p) => [
            "{$p}.value" => 'required|integer|min:0|max:100000000',
            "{$p}.suffix" => 'nullable|string|max:3',
            "{$p}.label_ar" => 'required|string|max:80',
            "{$p}.label_en" => 'nullable|string|max:80',
        ];

        return [
            'site_name_ar' => 'sometimes|required|string|max:120',
            'site_name_en' => 'nullable|string|max:120',
            'footer_about_ar' => 'nullable|string|max:400',
            'footer_about_en' => 'nullable|string|max:400',
            'phone1' => $phone,
            'phone2' => $phone,
            'whatsapp' => $phone,
            'email' => 'nullable|email|max:120',
            'inbox_email' => 'nullable|email|max:120',
            'address_ar' => $text,
            'address_en' => $text,
            'hours_ar' => $text,
            'hours_en' => $text,
            'map_url' => $url,
            'facebook' => $url,
            'youtube' => $url,
            'linkedin' => $url,
            'instagram' => $url,
            'stats' => 'sometimes|array|max:4',
            ...$stat('stats.*'),
            'about_title_ar' => 'nullable|string|max:120',
            'about_title_en' => 'nullable|string|max:120',
            'about_body_ar' => 'nullable|string|max:3000',
            'about_body_en' => 'nullable|string|max:3000',
            'timeline' => 'sometimes|array|max:12',
            'timeline.*.year' => 'required|integer|min:1950|max:2100',
            'timeline.*.title_ar' => 'required|string|max:80',
            'timeline.*.title_en' => 'nullable|string|max:80',
            'timeline.*.text_ar' => 'nullable|string|max:300',
            'timeline.*.text_en' => 'nullable|string|max:300',
            'seo_title_ar' => 'nullable|string|max:70',
            'seo_title_en' => 'nullable|string|max:70',
            'seo_description_ar' => 'nullable|string|max:170',
            'seo_description_en' => 'nullable|string|max:170',
        ];
    }

    /** @return array<string, mixed> */
    public static function all(): array
    {
        $stored = Cache::rememberForever(self::CACHE_KEY, fn () => Setting::query()->pluck('value', 'key')->all());

        return array_merge(self::DEFAULTS, array_intersect_key($stored, self::DEFAULTS));
    }

    /** @param array<string, mixed> $values validated input */
    public static function update(array $values): void
    {
        $values = array_intersect_key($values, self::DEFAULTS);

        DB::transaction(function () use ($values): void {
            foreach ($values as $key => $value) {
                Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
            }
        });

        Cache::forget(self::CACHE_KEY);
    }

    /** The localized shape the public site renders (header, footer, contact sprout, numbers, SEO). */
    public static function forSite(string $locale): array
    {
        $s = self::all();
        $tr = fn (string $key) => ($s["{$key}_{$locale}"] ?? null) ?: ($s["{$key}_".($locale === 'ar' ? 'en' : 'ar')] ?? null);
        $stat = fn (array $x) => [
            'value' => (int) $x['value'],
            'suffix' => $x['suffix'] ?? '',
            'label' => ($x["label_{$locale}"] ?? null) ?: $x['label_ar'],
        ];

        return [
            'site_name' => $tr('site_name'),
            'footer_about' => $tr('footer_about'),
            'contact' => [
                'phones' => array_values(array_filter([$s['phone1'], $s['phone2']])),
                'whatsapp' => $s['whatsapp'],
                'email' => $s['email'],
                'address' => $tr('address'),
                'hours' => $tr('hours'),
                'map_url' => $s['map_url'],
            ],
            'social' => (object) array_filter([
                'facebook' => $s['facebook'],
                'youtube' => $s['youtube'],
                'linkedin' => $s['linkedin'],
                'instagram' => $s['instagram'],
            ]),
            'stats' => array_map($stat, $s['stats'] ?? []),
            'seo' => ['title' => $tr('seo_title'), 'description' => $tr('seo_description')],
        ];
    }

    /** The about page story and milestones; paragraphs are split on blank lines. */
    public static function about(string $locale): array
    {
        $s = self::all();
        $other = $locale === 'ar' ? 'en' : 'ar';
        $tr = fn (array $row, string $key) => ($row["{$key}_{$locale}"] ?? null) ?: ($row["{$key}_{$other}"] ?? null);
        $body = (string) $tr($s, 'about_body');

        return [
            'title' => $tr($s, 'about_title'),
            'paragraphs' => array_values(array_filter(array_map('trim', preg_split('/\R{2,}/u', $body) ?: []))),
            'timeline' => array_map(fn (array $t) => ['year' => (int) $t['year'], 'title' => $tr($t, 'title'), 'text' => $tr($t, 'text')], $s['timeline'] ?? []),
        ];
    }
}
