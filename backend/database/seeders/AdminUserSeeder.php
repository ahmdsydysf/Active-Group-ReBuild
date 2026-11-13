<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** Creates the first admin from ADMIN_EMAIL / ADMIN_PASSWORD in .env. Never ships a default password. */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD are empty, so no admin account was created.');

            return;
        }

        if (strlen($password) < 12) {
            $this->command?->error('ADMIN_PASSWORD must be at least 12 characters.');

            return;
        }

        User::query()->updateOrCreate(['email' => $email], [
            'name' => env('ADMIN_NAME', 'Admin'),
            'password' => $password,
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }
}
