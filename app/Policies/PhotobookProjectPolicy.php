<?php

namespace App\Policies;

use App\Models\PhotobookProject;
use App\Models\User;

class PhotobookProjectPolicy
{
    /** Everyone has a project list; its contents are owner-filtered by visibleTo(). */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, PhotobookProject $project): bool
    {
        return $user->isAdmin()
            || $this->photographerOwns($user, $project)
            || $this->customerOwns($user, $project);
    }

    /** Project creation arrives with the Design Engine (Phase 2). */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, PhotobookProject $project): bool
    {
        return $this->photographerOwns($user, $project);
    }

    public function delete(User $user, PhotobookProject $project): bool
    {
        return $this->photographerOwns($user, $project);
    }

    private function photographerOwns(User $user, PhotobookProject $project): bool
    {
        return $user->isPhotographer()
            && $project->photographer_profile_id !== null
            && $project->photographer_profile_id === $user->photographerProfile?->id;
    }

    private function customerOwns(User $user, PhotobookProject $project): bool
    {
        return $user->isCustomer()
            && $project->customer_id !== null
            && $project->customer?->user_id === $user->id;
    }
}
