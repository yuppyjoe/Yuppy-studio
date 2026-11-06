<?php

namespace App\Models;

use Database\Factories\AiRequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Placeholder for AI calls made in later phases. Not used in Phase 1. */
#[Fillable(['type', 'status', 'provider', 'model', 'input', 'output', 'error', 'input_tokens', 'output_tokens'])]
class AiRequest extends Model
{
    /** @use HasFactory<AiRequestFactory> */
    use HasFactory;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'input' => 'array',
            'output' => 'array',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<PhotobookProject, $this> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(PhotobookProject::class, 'photobook_project_id');
    }
}
