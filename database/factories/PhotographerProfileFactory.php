<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\PhotographerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhotographerProfile>
 */
class PhotographerProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => UserRole::Photographer]),
            'studio_name' => fake()->unique()->lastName().' Studio',
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'website' => null,
            'city' => fake()->city(),
            'bio' => null,
        ];
    }
}
