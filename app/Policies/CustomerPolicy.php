<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPhotographer();
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->isAdmin()
            || $this->photographerOwns($user, $customer)
            || ($user->isCustomer() && $customer->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isPhotographer() && $user->photographerProfile !== null;
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->photographerOwns($user, $customer);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $this->photographerOwns($user, $customer);
    }

    private function photographerOwns(User $user, Customer $customer): bool
    {
        return $user->isPhotographer()
            && $customer->photographer_profile_id !== null
            && $customer->photographer_profile_id === $user->photographerProfile?->id;
    }
}
