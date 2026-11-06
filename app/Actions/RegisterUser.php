<?php

namespace App\Actions;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\PhotographerProfile;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Creates a self-registered account with the record its role needs.
 * Only photographer and customer roles can be self-registered.
 */
class RegisterUser
{
    public function __construct(private readonly AuditLogger $audit) {}

    /**
     * @param  array{name: string, email: string, password: string}  $data
     */
    public function handle(array $data, UserRole $role): User
    {
        if (! in_array($role, UserRole::selfRegistrable(), true)) {
            throw new InvalidArgumentException("Role [{$role->value}] cannot self-register.");
        }

        return DB::transaction(function () use ($data, $role) {
            $user = new User($data);
            $user->role = $role;
            $user->save();

            if ($role === UserRole::Photographer) {
                $profile = new PhotographerProfile([
                    'studio_name' => $user->name,
                    'email' => $user->email,
                ]);
                $profile->user()->associate($user)->save();
            } else {
                $customer = new Customer(['name' => $user->name, 'email' => $user->email]);
                $customer->user()->associate($user)->save();
            }

            $this->audit->log('auth.registered', $user, new: ['role' => $role->value], actor: $user);

            return $user;
        });
    }
}
