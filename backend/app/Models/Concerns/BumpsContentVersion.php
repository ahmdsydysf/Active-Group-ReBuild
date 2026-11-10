<?php

namespace App\Models\Concerns;

use App\Support\ContentVersion;

/**
 * Any change to public content bumps the content version, which invalidates the public API cache.
 */
trait BumpsContentVersion
{
    protected static function bootBumpsContentVersion(): void
    {
        static::saved(fn () => ContentVersion::bump());
        static::deleted(fn () => ContentVersion::bump());
    }
}
