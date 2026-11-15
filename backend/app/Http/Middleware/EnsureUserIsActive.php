<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** A disabled account loses access at once, even with a token that has not expired yet. */
final class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->is_active) {
            $request->user()?->currentAccessToken()?->delete();
            abort(401, 'Unauthenticated.');
        }

        return $next($request);
    }
}
