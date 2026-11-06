<?php

use App\Enums\BrandingMode;
use App\Models\BrandingProfile;
use App\Models\Customer;
use App\Models\PhotobookProject;
use App\Services\BrandingResolver;

beforeEach(function () {
    $this->yuppy = BrandingProfile::factory()->yuppy()->create();
    $this->resolver = new BrandingResolver;
});

test('a photographer project resolves to the photographer\'s own branding', function () {
    $photographer = photographer(withBranding: true);
    $project = PhotobookProject::factory()->forPhotographer($photographer->photographerProfile)->create();

    $branding = $this->resolver->resolve($project);

    expect($branding->id)->toBe($photographer->photographerProfile->brandingProfile->id)
        ->and($branding->is_yuppy)->toBeFalse();
});

test('a photographer project never returns Yuppy branding', function (BrandingMode $mode, bool $hasBranding) {
    $photographer = photographer(withBranding: $hasBranding);
    $project = PhotobookProject::factory()->forPhotographer($photographer->photographerProfile)
        ->create(['branding_mode' => $mode]);

    $branding = $this->resolver->resolve($project);

    expect($branding?->is_yuppy)->not->toBeTrue()
        ->and($branding?->id)->not->toBe($this->yuppy->id);
})->with([
    'studio mode, studio has branding' => [BrandingMode::Photographer, true],
    'studio mode, no studio branding yet' => [BrandingMode::Photographer, false],
    'unbranded' => [BrandingMode::Unbranded, true],
]);

test('a photographer project cannot be saved with Yuppy branding', function () {
    $photographer = photographer();

    expect(fn () => PhotobookProject::factory()->forPhotographer($photographer->photographerProfile)
        ->create(['branding_mode' => BrandingMode::Yuppy]))
        ->toThrow(DomainException::class);
});

test('even a corrupted photographer row with yuppy mode resolves to nothing', function () {
    $photographer = photographer(withBranding: true);
    $project = PhotobookProject::factory()->forPhotographer($photographer->photographerProfile)->create();

    // Bypass model events, as a bad import or manual SQL might.
    PhotobookProject::query()->whereKey($project->id)->update(['branding_mode' => 'yuppy']);

    expect($this->resolver->resolve($project->fresh()))->toBeNull();
});

test('a customer project linked to a photographer never returns Yuppy branding', function () {
    $photographer = photographer(withBranding: true);
    $customer = Customer::factory()->for($photographer->photographerProfile)->create();
    $project = PhotobookProject::factory()->forCustomer($customer)->make(['branding_mode' => BrandingMode::Photographer]);
    $project->photographer_profile_id = $photographer->photographerProfile->id;
    $project->save();

    expect($this->resolver->resolve($project)->is_yuppy)->toBeFalse();
});

test('a Yuppy-owned project with Yuppy mode resolves the Yuppy brand', function () {
    $project = PhotobookProject::factory()->create(['branding_mode' => BrandingMode::Yuppy]);

    expect($this->resolver->resolve($project)->id)->toBe($this->yuppy->id);
});

test('an unbranded Yuppy project resolves to nothing', function () {
    $project = PhotobookProject::factory()->create(['branding_mode' => BrandingMode::Unbranded]);

    expect($this->resolver->resolve($project))->toBeNull();
});
