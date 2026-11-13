<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        RateLimiter::for('public', fn (Request $r) => Limit::perMinute(120)->by($r->ip()));
        RateLimiter::for('forms', fn (Request $r) => Limit::perMinute(5)->by($r->ip()));
        RateLimiter::for('admin', fn (Request $r) => Limit::perMinute(240)->by($r->user()?->id ?: $r->ip()));
        // Per email and per IP, so one attacker cannot lock a real admin out from elsewhere.
        RateLimiter::for('login', fn (Request $r) => [
            Limit::perMinute(5)->by('login:'.mb_strtolower((string) $r->input('email')).'|'.$r->ip()),
            Limit::perMinute(20)->by('login-ip:'.$r->ip()),
        ]);
    }
}
