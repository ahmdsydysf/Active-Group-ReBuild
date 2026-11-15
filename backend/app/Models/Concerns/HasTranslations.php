<?php

namespace App\Models\Concerns;

/**
 * Bilingual columns are stored as `<field>_ar` and `<field>_en`.
 * `tr()` returns the value for the given locale and falls back to the other language.
 */
trait HasTranslations
{
    public function tr(string $field, string $locale): ?string
    {
        $fallback = $locale === 'ar' ? 'en' : 'ar';
        $value = $this->getAttribute("{$field}_{$locale}");

        if ($value === null || $value === '') {
            $value = $this->getAttribute("{$field}_{$fallback}");
        }

        return $value === '' ? null : $value;
    }
}
