<?php

namespace App\Support;

/**
 * Keeps rich text safe and clean: an allow-list of tags, no inline styles, scripts or event handlers.
 */
final class HtmlSanitizer
{
    private const ALLOWED = '<p><br><strong><b><em><i><u><s><h2><h3><h4><h5><ul><ol><li><a><blockquote><img><figure><figcaption><table><thead><tbody><tr><th><td><hr><span>';

    public static function clean(?string $html): ?string
    {
        if ($html === null) {
            return null;
        }

        $html = preg_replace('#<(script|style|iframe|object|embed)[^>]*>.*?</\1>#is', '', $html) ?? '';
        $html = strip_tags($html, self::ALLOWED);
        // Drop event handlers, inline styles, classes and ids.
        $html = preg_replace('/\s+(on\w+|style|class|id)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? '';
        // Neutralise javascript: URLs.
        $html = preg_replace('/(href|src)\s*=\s*(["\']?)\s*javascript:[^"\'>\s]*/i', '$1=$2#', $html) ?? '';
        $html = str_replace('&nbsp;', ' ', $html);
        $html = preg_replace('#(<p>\s*(<br\s*/?>)?\s*</p>\s*)+$#i', '', trim($html)) ?? '';

        return trim($html) === '' ? null : trim($html);
    }
}
