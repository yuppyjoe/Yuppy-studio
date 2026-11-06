<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Auth\Events\Logout;

class RecordLogout
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function handle(Logout $event): void
    {
        if ($event->user instanceof User) {
            $this->audit->log('auth.logout', $event->user, actor: $event->user);
        }
    }
}
