<?php

namespace App\Models;

use App\Enums\BookOrientation;
use Database\Factories\BookFormatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Nominal sizes only (e.g. "8x10 in"). Production measurements belong to
 * verified manufacturer templates.
 */
#[Fillable(['name', 'slug', 'nominal_width_in', 'nominal_height_in', 'orientation', 'sort_order', 'is_active'])]
class BookFormat extends Model
{
    /** @use HasFactory<BookFormatFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'nominal_width_in' => 'decimal:2',
            'nominal_height_in' => 'decimal:2',
            'orientation' => BookOrientation::class,
            'is_active' => 'boolean',
        ];
    }

    /** @return HasMany<ManufacturerTemplate, $this> */
    public function manufacturerTemplates(): HasMany
    {
        return $this->hasMany(ManufacturerTemplate::class);
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
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
