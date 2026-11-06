<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Customer;
use App\Models\PhotographerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Customer,
            'status' => UserStatus::Active,
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => UserRole::Admin]);
    }

    /** A photographer with their studio profile. */
    public function photographer(): static
    {
        return $this->state(fn () => ['role' => UserRole::Photographer])
            ->afterCreating(function (User $user): void {
                PhotographerProfile::factory()->for($user)->create();
            });
    }

    /** A self-registered customer with their customer record. */
    public function customer(): static
    {
        return $this->state(fn () => ['role' => UserRole::Customer])
            ->afterCreating(function (User $user): void {
                Customer::factory()->for($user)->create([
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
            });
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => UserStatus::Suspended]);
    }
}
