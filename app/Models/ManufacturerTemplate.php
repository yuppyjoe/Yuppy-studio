<?php

namespace App\Models;

use Database\Factories\ManufacturerTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Measurement columns are nullable and is_verified defaults to false.
 * Values must come from a manufacturer's spec sheet; never invent them.
 * Verification fields are not fillable; they are set by an explicit admin action.
 */
#[Fillable([
    'book_format_id', 'manufacturer_name', 'product_code',
    'trim_width_mm', 'trim_height_mm', 'bleed_mm', 'safe_margin_mm', 'gutter_mm', 'spine_width_mm',
    'dpi', 'min_pages', 'max_pages', 'page_increment', 'notes',
])]
class ManufacturerTemplate extends Model
{
    /** @use HasFactory<ManufacturerTemplateFactory> */
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'is_verified' => false,
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<BookFormat, $this> */
    public function bookFormat(): BelongsTo
    {
        return $this->belongsTo(BookFormat::class);
    }

    /** @return BelongsTo<User, $this> */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }
}
