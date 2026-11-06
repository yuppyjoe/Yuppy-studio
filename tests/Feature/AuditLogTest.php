<?php

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogger;
use Livewire\Volt\Volt;

test('logging in writes an audit entry and stamps last_login_at', function () {
    $user = User::factory()->customer()->create();

    Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'password')
        ->call('login');

    $entry = AuditLog::where('event', 'auth.login')->sole();
    expect($entry->user_id)->toBe($user->id)
        ->and($entry->auditable_id)->toBe($user->id)
        ->and($entry->ip_address)->not->toBeNull()
        ->and($user->fresh()->last_login_at)->not->toBeNull();
});

test('failed logins do not write a login entry', function () {
    $user = User::factory()->create();

    Volt::test('pages.auth.login')
        ->set('form.email', $user->email)
        ->set('form.password', 'wrong')
        ->call('login');

    expect(AuditLog::where('event', 'auth.login')->count())->toBe(0);
});

test('logging out writes an audit entry', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('logout'));

    expect(AuditLog::where('event', 'auth.logout')->where('user_id', $user->id)->exists())->toBeTrue();
});

test('studio profile changes are audited with old and new values', function () {
    $user = photographer();
    $this->actingAs($user);

    Volt::test('pages.studio.profile')
        ->set('studio_name', 'Renamed Studio')
        ->set('phone', '+254 711 111111')
        ->set('email', 'renamed@example.com')
        ->call('saveStudio')
        ->assertHasNoErrors();

    $entry = AuditLog::where('event', 'photographer_profile.updated')->sole();
    expect($entry->user_id)->toBe($user->id)
        ->and($entry->new_values['studio_name'])->toBe('Renamed Studio')
        ->and($entry->old_values)->toHaveKey('studio_name');
});

test('branding changes are audited', function () {
    $this->actingAs(photographer());

    Volt::test('pages.studio.profile')
        ->set('branding.brand_name', 'New Brand')
        ->set('branding.email', 'brand@example.com')
        ->set('branding.phone', '0700 000 000')
        ->call('saveBranding')
        ->assertHasNoErrors();

    expect(AuditLog::where('event', 'branding_profile.updated')->sole()->new_values['brand_name'])->toBe('New Brand');
});

test('account profile changes are audited and passwords are never logged', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Volt::test('profile.update-profile-information-form')
        ->set('name', 'Changed Name')
        ->set('email', $user->email)
        ->call('updateProfileInformation');

    $entry = AuditLog::where('event', 'account.updated')->sole();
    expect($entry->new_values)->toBe(['name' => 'Changed Name']);

    app(AuditLogger::class)->log('test.event', $user, ['password' => 'x'], ['password' => 'y', 'name' => 'n']);
    expect(AuditLog::where('event', 'test.event')->sole()->new_values)->toBe(['name' => 'n']);
});

test('saving without changes does not write an audit entry', function () {
    $this->actingAs(photographer());

    Volt::test('pages.studio.profile')
        ->set('phone', '0700 000 000')
        ->set('email', 'same@example.com')
        ->call('saveStudio');
    Volt::test('pages.studio.profile')->call('saveStudio');

    expect(AuditLog::where('event', 'photographer_profile.updated')->count())->toBe(1);
});
