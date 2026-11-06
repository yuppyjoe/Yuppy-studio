<?php

use App\Http\Controllers\BrandingLogoController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome')->name('home');

Route::middleware('auth')->group(function () {
    Route::post('logout', function (Logout $logout) {
        $logout();

        return redirect('/');
    })->name('logout');

    // Logos live on the private disk; this route is the only way to read them.
    Route::get('files/branding/{brandingProfile}/logo', BrandingLogoController::class)
        ->name('files.branding.logo');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Volt::route('/', 'pages.admin.dashboard')->name('dashboard');
        Volt::route('branding', 'pages.admin.branding')->name('branding');
        Volt::route('photographers', 'pages.admin.photographers')->name('photographers');
        Volt::route('customers', 'pages.admin.customers')->name('customers');
    });

    Route::middleware('role:photographer')->prefix('studio')->name('studio.')->group(function () {
        Volt::route('/', 'pages.studio.dashboard')->name('dashboard');
        Volt::route('profile', 'pages.studio.profile')->name('profile');

        Volt::route('clients', 'pages.studio.customers.index')->name('customers.index');
        Volt::route('clients/new', 'pages.studio.customers.form')->name('customers.create');
        Volt::route('clients/{customer}/edit', 'pages.studio.customers.form')->name('customers.edit');

        Volt::route('projects', 'pages.studio.projects.index')->name('projects.index');
        Volt::route('projects/{project}', 'pages.studio.projects.show')->name('projects.show');
    });

    Route::middleware('role:customer')->prefix('my')->name('customer.')->group(function () {
        Volt::route('/', 'pages.customer.dashboard')->name('dashboard');
        Volt::route('photobooks/{project}', 'pages.customer.project')->name('projects.show');
    });
});

require __DIR__.'/auth.php';
