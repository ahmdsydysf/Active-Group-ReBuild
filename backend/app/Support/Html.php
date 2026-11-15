<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Cleans rich text from the dashboard editor before it is stored. Only the tags the editor
 * produces survive; scripts, styles, event attributes and unknown tags are removed.
 */
final class Html
{
    private static ?HTMLPurifier $purifier = null;

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $clean = trim(self::purifier()->purify($html));

        return $clean === '' || $clean === '<p></p>' ? null : $clean;
    }

    private static function purifier(): HTMLPurifier
    {
        if (! self::$purifier) {
            $config = HTMLPurifier_Config::createDefault();
            $config->set('HTML.Allowed', 'p,br,h2,h3,strong,b,em,i,u,ul,ol,li,blockquote,a[href],table,thead,tbody,tr,th,td');
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true]);
            $config->set('HTML.TargetBlank', true);
            $config->set('HTML.Nofollow', true);
            $config->set('AutoFormat.RemoveEmpty', true);
            $config->set('Cache.DefinitionImpl', null);
            self::$purifier = new HTMLPurifier($config);
        }

        return self::$purifier;
    }
}
