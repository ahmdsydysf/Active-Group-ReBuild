<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\App;

/**
 * Bilingual columns are stored as `<field>_ar` and `<field>_en`.
 * `tr()` returns the value for the current locale and falls back to the other language.
 */
trait HasTranslations
{
    public function tr(string $field, ?string $locale = null): ?string
    {
        $locale ??= App::getLocale();
        $fallback = $locale === 'ar' ? 'en' : 'ar';

        $value = $this->getAttribute("{$field}_{$locale}");

        if ($value === null || $value === '') {
            $value = $this->getAttribute("{$field}_{$fallback}");
        }

        return $value === '' ? null : $value;
    }
}
