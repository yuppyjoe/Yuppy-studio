<?php

use App\Models\User;

test('each role lands on its own dashboard', function (string $factoryState, string $route, string $expectedText) {
    $user = User::factory()->{$factoryState}()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route($route));

    $this->actingAs($user)
        ->get(route($route))
        ->assertOk()
        ->assertSee($expectedText);
})->with([
    'admin' => ['admin', 'admin.dashboard', 'Yuppy administration'],
    'photographer' => ['photographer', 'studio.dashboard', 'Studio setup'],
    'customer' => ['customer', 'customer.dashboard', 'My photobooks'],
]);

test('guests are sent to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

test('unverified users must verify their email first', function () {
    $user = User::factory()->photographer()->unverified()->create();

    $this->actingAs($user)
        ->get(route('studio.dashboard'))
        ->assertRedirect(route('verification.notice'));
});

test('sidebar navigation only shows the signed-in role\'s areas', function () {
    $this->actingAs(photographer())
        ->get(route('studio.dashboard'))
        ->assertSee(route('studio.customers.index'))
        ->assertDontSee(route('admin.photographers'));
});
