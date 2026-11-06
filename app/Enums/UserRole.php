<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Photographer = 'photographer';
    case Customer = 'customer';

    /**
     * Roles a visitor may choose when registering. Admins are seeded only.
     *
     * @return list<self>
     */
    public static function selfRegistrable(): array
    {
        return [self::Photographer, self::Customer];
    }

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Photographer => 'Photographer',
            self::Customer => 'Customer',
        };
    }

    /** Named route of this role's home dashboard. */
    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Admin => 'admin.dashboard',
            self::Photographer => 'studio.dashboard',
            self::Customer => 'customer.dashboard',
        };
    }
}
