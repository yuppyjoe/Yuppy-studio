<?php

namespace Database\Factories;

use App\Models\BrandingProfile;
use App\Models\PhotographerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrandingProfile>
 */
class BrandingProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photographer_profile_id' => PhotographerProfile::factory(),
            'is_yuppy' => false,
            'brand_name' => fake()->company(),
            'tagline' => null,
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => null,
            'social_links' => ['instagram' => '@'.fake()->userName()],
            'logo_path' => null,
            'logo_mime' => null,
        ];
    }

    public function yuppy(): static
    {
        return $this->state(fn () => [
            'photographer_profile_id' => null,
            'is_yuppy' => true,
            'brand_name' => 'Yuppy',
        ]);
    }
}
