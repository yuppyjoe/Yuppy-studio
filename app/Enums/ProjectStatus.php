<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case InDesign = 'in_design';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::InDesign => 'In design',
            self::InReview => 'In review',
            self::Approved => 'Approved',
            self::Archived => 'Archived',
        };
    }
}
