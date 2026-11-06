<?php

namespace App\Models;

use App\Enums\BrandingMode;
use App\Enums\ProjectOwnerType;
use App\Enums\ProjectStatus;
use Database\Factories\PhotobookProjectFactory;
use DomainException;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Phase 1 stub. Ownership columns (owner_type, photographer_profile_id,
 * customer_id, created_by) are never mass-assigned from user input.
 *
 * @property ProjectOwnerType $owner_type
 * @property BrandingMode $branding_mode
 * @property ProjectStatus $status
 */
#[Fillable(['title', 'branding_mode', 'occasion_id', 'book_format_id', 'status'])]
class PhotobookProject extends Model
{
    /** @use HasFactory<PhotobookProjectFactory> */
    use HasFactory, SoftDeletes;

    /** @var array<string, mixed> */
    protected $attributes = [
        'branding_mode' => 'unbranded',
        'status' => 'draft',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'owner_type' => ProjectOwnerType::class,
            'branding_mode' => BrandingMode::class,
            'status' => ProjectStatus::class,
        ];
    }

    protected static function booted(): void
    {
        // Defence in depth for the branding rule (see BrandingResolver).
        static::saving(function (self $project): void {
            if ($project->isPhotographerLinked() && $project->branding_mode === BrandingMode::Yuppy) {
                throw new DomainException('A photographer project cannot use Yuppy branding.');
            }
        });
    }

    public function isPhotographerLinked(): bool
    {
        return $this->owner_type === ProjectOwnerType::Photographer
            || $this->photographer_profile_id !== null;
    }

    /** @return BelongsTo<PhotographerProfile, $this> */
    public function photographerProfile(): BelongsTo
    {
        return $this->belongsTo(PhotographerProfile::class);
    }

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<Occasion, $this> */
    public function occasion(): BelongsTo
    {
        return $this->belongsTo(Occasion::class);
    }

    /** @return BelongsTo<BookFormat, $this> */
    public function bookFormat(): BelongsTo
    {
        return $this->belongsTo(BookFormat::class);
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
            $user->isCustomer() => $query->whereHas(
                'customer',
                fn (Builder $customer) => $customer->where('user_id', $user->id),
            ),
            default => $query->whereRaw('1 = 0'),
        };
    }
}
