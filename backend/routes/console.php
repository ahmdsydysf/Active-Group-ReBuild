<?php

use App\Models\User;
use App\Support\Publisher;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

// Runs from one cPanel cron line: * * * * * php /path/to/backend/artisan schedule:run
Schedule::command('sanctum:prune-expired --hours=24')->daily();

// Scheduled articles appear on the static site only after a rebuild, so publish when one comes due.
Schedule::call(function (): void {
    if (Publisher::configured() && Publisher::hasDuePosts()) {
        Publisher::publish();
    }
})->name('publish-due-posts')->everyFifteenMinutes()->withoutOverlapping();

// Creates an admin, or resets an existing one's password, by asking in the terminal.
// Safer than .env for passwords with #, spaces or quotes, which .env can cut short.
Artisan::command('admin:create', function (): int {
    $email = mb_strtolower(trim((string) $this->ask('Email')));
    $name = (string) $this->ask('Name', User::query()->where('email', $email)->value('name') ?? 'مدير الموقع');
    $password = (string) $this->secret('Password (12+ characters)');

    $check = Validator::make(compact('email', 'password'), ['email' => 'required|email|max:120', 'password' => ['required', Password::min(12)]]);
    if ($check->fails()) {
        foreach ($check->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user = User::query()->updateOrCreate(['email' => $email], ['name' => $name, 'password' => $password, 'role' => User::ROLE_ADMIN, 'is_active' => true]);
    $this->info(($user->wasRecentlyCreated ? 'Admin created: ' : 'Password updated: ').$email);

    return 0;
})->purpose('Create an admin account or reset its password');
