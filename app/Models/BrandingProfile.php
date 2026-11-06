<?php

namespace App\Models;

use Database\Factories\BrandingProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A photographer's studio brand, or (photographer_profile_id = null,
 * is_yuppy = true) the single Yuppy house brand.
 */
#[Fillable(['brand_name', 'tagline', 'email', 'phone', 'website', 'social_links'])]
class BrandingProfile extends Model
{
    /** @use HasFactory<BrandingProfileFactory> */
    use HasFactory;

    /** Social networks offered on branding forms. */
    public const SOCIAL_NETWORKS = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'tiktok' => 'TikTok',
        'x' => 'X',
        'youtube' => 'YouTube',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_yuppy' => 'boolean',
            'social_links' => 'array',
        ];
    }

    /** @return BelongsTo<PhotographerProfile, $this> */
    public function photographerProfile(): BelongsTo
    {
        return $this->belongsTo(PhotographerProfile::class);
    }

    public static function yuppy(): ?self
    {
        return static::query()->where('is_yuppy', true)->whereNull('photographer_profile_id')->first();
    }

    public function hasLogo(): bool
    {
        return $this->logo_path !== null;
    }

    public function logoUrl(): ?string
    {
        return $this->hasLogo() ? route('files.branding.logo', $this) : null;
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match (true) {
            $user->isAdmin() => $query,
            $user->isPhotographer() => $query->where(
                'photographer_profile_id',
                $user->photographerProfile?->id ?? 0,
            ),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
