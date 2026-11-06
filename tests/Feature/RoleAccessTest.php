<?php

$adminRoutes = ['admin.dashboard', 'admin.branding', 'admin.photographers', 'admin.customers'];
$studioRoutes = ['studio.dashboard', 'studio.profile', 'studio.customers.index', 'studio.customers.create', 'studio.projects.index'];

test('customers get 403 on admin routes', function (string $route) {
    $this->actingAs(customerUser())->get(route($route))->assertForbidden();
})->with($adminRoutes);

test('customers get 403 on photographer routes', function (string $route) {
    $this->actingAs(customerUser())->get(route($route))->assertForbidden();
})->with($studioRoutes);

test('photographers get 403 on admin routes', function (string $route) {
    $this->actingAs(photographer())->get(route($route))->assertForbidden();
})->with($adminRoutes);

test('admins get 403 on photographer and customer areas', function (string $route) {
    $this->actingAs(admin())->get(route($route))->assertForbidden();
})->with([...$studioRoutes, 'customer.dashboard']);

test('each role can open every page in its own area', function () use ($adminRoutes, $studioRoutes) {
    $admin = admin();
    foreach ($adminRoutes as $route) {
        $this->actingAs($admin)->get(route($route))->assertOk();
    }

    $photographer = photographer();
    foreach ($studioRoutes as $route) {
        $this->actingAs($photographer)->get(route($route))->assertOk();
    }

    $this->actingAs(customerUser())->get(route('customer.dashboard'))->assertOk();
});
