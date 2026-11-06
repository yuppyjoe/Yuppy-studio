<?php

namespace Database\Seeders;

use App\Models\BrandingProfile;
use App\Services\LogoStorage;
use Illuminate\Database\Seeder;

class YuppyBrandingSeeder extends Seeder
{
    public function run(LogoStorage $logos): void
    {
        $branding = BrandingProfile::yuppy() ?? new BrandingProfile;

        $branding->forceFill([
            'photographer_profile_id' => null,
            'is_yuppy' => true,
        ]);

        if (! $branding->exists) {
            $branding->fill([
                'brand_name' => 'Yuppy',
                'tagline' => 'Photobooks, beautifully made.',
            ]);
        }

        $branding->save();

        if (! $branding->hasLogo()) {
            $logos->storeFromPath($branding, database_path('seeders/assets/yuppy-logo-placeholder.png'));
        }
    }
}
