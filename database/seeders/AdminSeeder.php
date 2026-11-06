<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Admins cannot self-register; they are created here.
 * In production SEED_ADMIN_PASSWORD must be set, otherwise this seeder is skipped.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('yuppy.seed_admin.email');
        $password = config('yuppy.seed_admin.password');

        if (blank($password)) {
            if (app()->isProduction()) {
                $this->command?->warn('SEED_ADMIN_PASSWORD is not set; admin account not created.');

                return;
            }

            $password = 'password';
        }

        $admin = User::firstOrNew(['email' => $email]);

        if (! $admin->exists) {
            $admin->forceFill([
                'name' => 'Yuppy Admin',
                'password' => $password,
                'email_verified_at' => now(),
            ]);
        }

        $admin->forceFill(['role' => UserRole::Admin, 'status' => UserStatus::Active])->save();
    }
}
