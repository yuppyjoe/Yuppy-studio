<?php

namespace App\Services;

use App\Enums\BrandingMode;
use App\Models\BrandingProfile;
use App\Models\PhotobookProject;

/**
 * Decides which branding a photobook project prints with.
 *
 * Business rule (non-negotiable): a project that belongs to or is linked to a
 * photographer must NEVER resolve the Yuppy house brand. It gets the
 * photographer's own studio branding, or nothing.
 */
class BrandingResolver
{
    public function resolve(PhotobookProject $project): ?BrandingProfile
    {
        if ($project->isPhotographerLinked()) {
            return $this->forPhotographerProject($project);
        }

        return $project->branding_mode === BrandingMode::Yuppy
            ? BrandingProfile::yuppy()
            : null;
    }

    private function forPhotographerProject(PhotobookProject $project): ?BrandingProfile
    {
        if ($project->branding_mode !== BrandingMode::Photographer) {
            return null;
        }

        $branding = $project->photographerProfile?->brandingProfile;

        // Belt and braces: even a corrupted row must never leak the house brand.
        if ($branding === null || $branding->is_yuppy || $branding->photographer_profile_id === null) {
            return null;
        }

        return $branding;
    }
}
