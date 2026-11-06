<?php

namespace App\Policies;

use App\Models\PhotographerProfile;
use App\Models\User;

class PhotographerProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, PhotographerProfile $profile): bool
    {
        return $user->isAdmin() || $this->owns($user, $profile);
    }

    /** Admin access is read-only in Phase 1. */
    public function update(User $user, PhotographerProfile $profile): bool
    {
        return $this->owns($user, $profile);
    }

    private function owns(User $user, PhotographerProfile $profile): bool
    {
        return $user->isPhotographer() && $profile->user_id === $user->id;
    }
}
