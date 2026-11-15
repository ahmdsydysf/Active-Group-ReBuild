<?php

namespace App\Models\Concerns;

use App\Support\Publisher;

/** Any change to content the public site shows marks the site as needing a publish. */
trait TouchesSite
{
    protected static function bootTouchesSite(): void
    {
        static::saved(fn () => Publisher::touch());
        static::deleted(fn () => Publisher::touch());
    }
}
