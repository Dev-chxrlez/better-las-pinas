<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class ProductionAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('production')) {
            return;
        }

        $email = env('INITIAL_ADMIN_EMAIL');
        $password = env('INITIAL_ADMIN_PASSWORD');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! is_string($password) || strlen($password) < 12) {
            $this->command?->warn('Initial administrator skipped: configure a valid email and a password of at least 12 characters.');
            return;
        }

        $admin = User::firstOrCreate(
            ['email' => strtolower($email)],
            ['name' => env('INITIAL_ADMIN_NAME', 'Better Las Piñas Administrator'), 'password' => $password],
        );

        if (! $admin->hasRole('system-administrator')) {
            $admin->assignRole('system-administrator');
        }
    }
}
