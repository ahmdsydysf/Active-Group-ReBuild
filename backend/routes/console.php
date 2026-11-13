<?php

use App\Support\Publisher;
use Illuminate\Support\Facades\Schedule;

// Runs from one cPanel cron line: * * * * * php /path/to/backend/artisan schedule:run
Schedule::command('sanctum:prune-expired --hours=24')->daily();

// Scheduled articles appear on the static site only after a rebuild, so publish when one comes due.
Schedule::call(function (): void {
    if (Publisher::configured() && Publisher::hasDuePosts()) {
        Publisher::publish();
    }
})->name('publish-due-posts')->everyFifteenMinutes()->withoutOverlapping();
