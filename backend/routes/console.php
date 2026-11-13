<?php

use Illuminate\Support\Facades\Schedule;

// Runs from one cPanel cron line: * * * * * php /path/to/backend/artisan schedule:run
Schedule::command('sanctum:prune-expired --hours=24')->daily();
