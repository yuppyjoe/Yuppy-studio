<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\BrandingProfile;
use App\Models\Customer;
use App\Models\PhotographerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Local/demo accounts. All passwords are "password". Never run in production.
 */
class DemoAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $studios = [
            [
                'name' => 'Amani Wanjiru', 'email' => 'amani@studio.test',
                'studio' => 'Amani Light Studio', 'city' => 'Nairobi',
                'tagline' => 'Honest light, lasting stories.', 'instagram' => '@amanilight',
            ],
            [
                'name' => 'Baraka Otieno', 'email' => 'baraka@studio.test',
                'studio' => 'Lakeside Frames', 'city' => 'Kisumu',
                'tagline' => 'Weddings and portraits by the lake.', 'instagram' => '@lakesideframes',
            ],
        ];

        foreach ($studios as $data) {
            $user = $this->user($data['name'], $data['email'], UserRole::Photographer);

            $profile = PhotographerProfile::updateOrCreate(['user_id' => $user->id], [
                'studio_name' => $data['studio'],
                'email' => $data['email'],
                'city' => $data['city'],
            ]);

            BrandingProfile::firstOrNew(['photographer_profile_id' => $profile->id])
                ->forceFill(['is_yuppy' => false])
                ->fill([
                    'brand_name' => $data['studio'],
                    'tagline' => $data['tagline'],
                    'email' => $data['email'],
                    'social_links' => ['instagram' => $data['instagram']],
                ])
                ->save();
        }

        foreach ([['Zawadi Njeri', 'zawadi@customer.test'], ['Kevin Mutua', 'kevin@customer.test']] as [$name, $email]) {
            $user = $this->user($name, $email, UserRole::Customer);

            Customer::firstOrNew(['user_id' => $user->id])
                ->forceFill(['user_id' => $user->id])
                ->fill(['name' => $name, 'email' => $email])
                ->save();
        }
    }

    private function user(string $name, string $email, UserRole $role): User
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => $name,
            'role' => $role,
            'password' => $user->exists ? $user->password : 'password',
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        return $user;
    }
}
