<?php

namespace Database\Factories;

use App\Enums\BrandingMode;
use App\Enums\ProjectOwnerType;
use App\Enums\ProjectStatus;
use App\Models\Customer;
use App\Models\PhotobookProject;
use App\Models\PhotographerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhotobookProject>
 */
class PhotobookProjectFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_type' => ProjectOwnerType::Yuppy,
            'photographer_profile_id' => null,
            'customer_id' => null,
            'branding_mode' => BrandingMode::Yuppy,
            'title' => fake()->sentence(3),
            'status' => ProjectStatus::Draft,
        ];
    }

    public function forPhotographer(?PhotographerProfile $profile = null): static
    {
        return $this->state(fn () => [
            'owner_type' => ProjectOwnerType::Photographer,
            'photographer_profile_id' => $profile->id ?? PhotographerProfile::factory(),
            'branding_mode' => BrandingMode::Photographer,
        ]);
    }

    public function forCustomer(?Customer $customer = null): static
    {
        return $this->state(fn () => [
            'owner_type' => ProjectOwnerType::Customer,
            'customer_id' => $customer->id ?? Customer::factory(),
            'branding_mode' => BrandingMode::Yuppy,
        ]);
    }
}
