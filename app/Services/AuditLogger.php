<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * The single entry point for writing the audit trail.
 */
class AuditLogger
{
    /** Attributes that must never be written to the audit trail. */
    private const REDACTED = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

    public function __construct(private readonly Request $request) {}

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     */
    public function log(string $event, ?Model $subject = null, array $old = [], array $new = [], ?User $actor = null): AuditLog
    {
        $actor ??= $this->request->user();

        return AuditLog::create([
            'user_id' => $actor?->getKey(),
            'event' => $event,
            'auditable_type' => $subject?->getMorphClass(),
            'auditable_id' => $subject?->getKey(),
            'old_values' => $this->clean($old) ?: null,
            'new_values' => $this->clean($new) ?: null,
            'ip_address' => $this->request->ip(),
            'user_agent' => Str::limit((string) $this->request->userAgent(), 500, ''),
            'created_at' => now(),
        ]);
    }

    /**
     * Log a model update with only the attributes that actually changed.
     * Call after save(); uses the model's tracked changes. Skips no-op saves.
     */
    public function logChanges(string $event, Model $subject, ?User $actor = null): ?AuditLog
    {
        $ignored = ['id', 'updated_at', 'created_at'];

        // A record created by this save: everything is new.
        if ($subject->wasRecentlyCreated && $subject->getChanges() === []) {
            return $this->log($event, $subject, [], Arr::except($subject->getAttributes(), $ignored), $actor);
        }

        $changes = Arr::except($subject->getChanges(), $ignored);

        if ($changes === []) {
            return null;
        }

        $old = Arr::only($subject->getPrevious(), array_keys($changes));

        return $this->log($event, $subject, $old, $changes, $actor);
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function clean(array $values): array
    {
        return Arr::except($values, self::REDACTED);
    }
}
