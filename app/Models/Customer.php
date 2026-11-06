<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Ownership columns (user_id, photographer_profile_id) are not fillable; they
 * are always set from the authenticated context, never from form input.
 */
#[Fillable(['name', 'email', 'phone', 'notes'])]
class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<PhotographerProfile, $this> */
    public function photographerProfile(): BelongsTo
    {
        return $this->belongsTo(PhotographerProfile::class);
    }

    /** @return HasMany<PhotobookProject, $this> */
    public function projects(): HasMany
    {
        return $this->hasMany(PhotobookProject::class);
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
                $user->photographerProfile->id ?? 0,
            ),
            $user->isCustomer() => $query->where('user_id', $user->id),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
