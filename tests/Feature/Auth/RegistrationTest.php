<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;
use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSeeVolt('pages.auth.register');
});

test('photographers can self-register and get a studio profile', function () {
    Volt::test('pages.auth.register')
        ->set('role', 'photographer')
        ->set('name', 'Test Photographer')
        ->set('email', 'photo@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::where('email', 'photo@example.com')->firstOrFail();
    expect($user->role)->toBe(UserRole::Photographer)
        ->and($user->photographerProfile)->not->toBeNull()
        ->and($user->photographerProfile->studio_name)->toBe('Test Photographer')
        ->and(AuditLog::where('event', 'auth.registered')->where('user_id', $user->id)->exists())->toBeTrue();
});

test('customers can self-register and get a customer record', function () {
    Volt::test('pages.auth.register')
        ->set('role', 'customer')
        ->set('name', 'Test Customer')
        ->set('email', 'customer@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertHasNoErrors();

    $user = User::where('email', 'customer@example.com')->firstOrFail();
    expect($user->role)->toBe(UserRole::Customer)
        ->and($user->customer)->not->toBeNull();
});

test('nobody can self-register as an admin', function () {
    Volt::test('pages.auth.register')
        ->set('role', 'admin')
        ->set('name', 'Sneaky')
        ->set('email', 'sneaky@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertHasErrors('role');

    $this->assertGuest();
    expect(User::where('email', 'sneaky@example.com')->exists())->toBeFalse();
});

test('role cannot be mass-assigned on a user', function () {
    $user = new User(['name' => 'X', 'email' => 'x@example.com', 'password' => 'password', 'role' => 'admin']);

    expect($user->role)->toBe(UserRole::Customer);
});
