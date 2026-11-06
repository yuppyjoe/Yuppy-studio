<?php

namespace App\Models;

use Database\Factories\PhotographerProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['studio_name', 'phone', 'email', 'website', 'city', 'bio'])]
class PhotographerProfile extends Model
{
    /** @use HasFactory<PhotographerProfileFactory> */
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasOne<BrandingProfile, $this> */
    public function brandingProfile(): HasOne
    {
        return $this->hasOne(BrandingProfile::class);
    }

    /** @return HasMany<Customer, $this> */
    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    /** @return HasMany<PhotobookProject, $this> */
    public function projects(): HasMany
    {
        return $this->hasMany(PhotobookProject::class);
    }

    /**
     * Owner filter: admins see all, photographers see only their own, others none.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return match (true) {
            $user->isAdmin() => $query,
            $user->isPhotographer() => $query->where('user_id', $user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
