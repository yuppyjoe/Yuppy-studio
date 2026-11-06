<?php

namespace App\Enums;

enum BrandingMode: string
{
    /** Printed with the Yuppy house brand. Never allowed on photographer projects. */
    case Yuppy = 'yuppy';

    /** Printed with the owning photographer's studio branding. */
    case Photographer = 'photographer';

    /** No branding at all (white label). */
    case Unbranded = 'unbranded';

    public function label(): string
    {
        return match ($this) {
            self::Yuppy => 'Yuppy',
            self::Photographer => 'Studio branding',
            self::Unbranded => 'Unbranded',
        };
    }
}
