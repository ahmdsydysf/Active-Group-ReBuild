<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

final class ContentVersion
{
    private const KEY = 'content_version';

    public static function current(): int
    {
        return (int) Cache::rememberForever(self::KEY, fn () => 1);
    }

    public static function bump(): void
    {
        Cache::forever(self::KEY, self::current() + 1);
    }
}
