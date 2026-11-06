<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;

/**
 * Sidebar navigation per role. The single place that maps roles to menu items,
 * so views never branch on roles themselves.
 */
class Navigation
{
    /**
     * @return list<array{label: string, route: string, icon: string, active: string}>
     */
    public static function for(User $user): array
    {
        return match ($user->role) {
            UserRole::Admin => [
                ['label' => 'Overview', 'route' => 'admin.dashboard', 'icon' => 'home', 'active' => 'admin.dashboard'],
                ['label' => 'Yuppy branding', 'route' => 'admin.branding', 'icon' => 'palette', 'active' => 'admin.branding'],
                ['label' => 'Photographers', 'route' => 'admin.photographers', 'icon' => 'camera', 'active' => 'admin.photographers'],
                ['label' => 'Customers', 'route' => 'admin.customers', 'icon' => 'users', 'active' => 'admin.customers'],
            ],
            UserRole::Photographer => [
                ['label' => 'Overview', 'route' => 'studio.dashboard', 'icon' => 'home', 'active' => 'studio.dashboard'],
                ['label' => 'Studio & branding', 'route' => 'studio.profile', 'icon' => 'palette', 'active' => 'studio.profile'],
                ['label' => 'Clients', 'route' => 'studio.customers.index', 'icon' => 'users', 'active' => 'studio.customers.*'],
                ['label' => 'Projects', 'route' => 'studio.projects.index', 'icon' => 'book', 'active' => 'studio.projects.*'],
            ],
            UserRole::Customer => [
                ['label' => 'My photobooks', 'route' => 'customer.dashboard', 'icon' => 'book', 'active' => 'customer.*'],
            ],
        };
    }
}
