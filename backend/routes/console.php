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

// Creates an admin, or resets an existing one's password.
//   php artisan admin:create                                   (asks for everything)
//   php artisan admin:create you@example.com --password="..."  (no prompts; handy in Git Bash on Windows)
// Safer than .env for passwords with #, spaces or quotes, which .env can cut short.
Artisan::command('admin:create {email?} {--name=} {--password=}', function (): int {
    $email = mb_strtolower(trim((string) ($this->argument('email') ?: $this->ask('Email'))));
    $existing = User::query()->where('email', $email)->value('name');
    $name = (string) ($this->option('name') ?: ($existing ?? $this->ask('Name', 'مدير الموقع')));

    // Some Windows terminals add a carriage return to typed input; it would become part of the password.
    $clean = fn (?string $v) => rtrim((string) $v, "\r\n");
    $password = $clean($this->option('password'));
    if ($password === '') {
        $password = $clean($this->secret('Password (12+ characters)'));
        if ($password !== $clean($this->secret('Same password again'))) {
            $this->error('The two passwords are different. Nothing was saved.');

            return 1;
        }
    }

    $check = Validator::make(compact('email', 'password'), ['email' => 'required|email|max:120', 'password' => ['required', Password::min(12)]]);
    if ($check->fails()) {
        foreach ($check->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user = User::query()->updateOrCreate(['email' => $email], ['name' => $name, 'password' => $password, 'role' => User::ROLE_ADMIN, 'is_active' => true]);
    $this->info(($user->wasRecentlyCreated ? 'Admin created: ' : 'Password updated: ').$email.' ('.mb_strlen($password).' characters)');
    $this->line('Database: '.config('database.default').' / '.config('database.connections.'.config('database.default').'.database'));

    return 0;
})->purpose('Create an admin account or reset its password');
