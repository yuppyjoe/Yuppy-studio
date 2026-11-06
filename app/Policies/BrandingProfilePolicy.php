<?php

namespace App\Policies;

use App\Enums\BrandingMode;
use App\Models\BrandingProfile;
use App\Models\PhotobookProject;
use App\Models\User;

class BrandingProfilePolicy
{
    public function view(User $user, BrandingProfile $branding): bool
    {
        return $user->isAdmin() || $this->ownsStudioBrand($user, $branding);
    }

    /** Admins manage only the Yuppy house brand; photographers only their own. */
    public function update(User $user, BrandingProfile $branding): bool
    {
        if ($branding->is_yuppy) {
            return $user->isAdmin();
        }

        return $this->ownsStudioBrand($user, $branding);
    }

    /**
     * Logos can be seen by those who can view the brand, plus: anyone signed in
     * may see the Yuppy house logo, and a customer may see the logo of a studio
     * whose branded photobook they own. Other photographers never can.
     */
    public function viewLogo(User $user, BrandingProfile $branding): bool
    {
        if ($this->view($user, $branding)) {
            return true;
        }

        if ($branding->is_yuppy) {
            return true;
        }

        return $user->isCustomer()
            && $branding->photographer_profile_id !== null
            && PhotobookProject::visibleTo($user)
                ->where('photographer_profile_id', $branding->photographer_profile_id)
                ->where('branding_mode', BrandingMode::Photographer)
                ->exists();
    }

    private function ownsStudioBrand(User $user, BrandingProfile $branding): bool
    {
        return $user->isPhotographer()
            && ! $branding->is_yuppy
            && $branding->photographer_profile_id !== null
            && $branding->photographer_profile_id === $user->photographerProfile?->id;
    }
}
