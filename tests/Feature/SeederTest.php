<?php

use App\Enums\UserRole;
use App\Models\BookFormat;
use App\Models\BrandingProfile;
use App\Models\DesignFamily;
use App\Models\ManufacturerTemplate;
use App\Models\Occasion;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('private'));

test('seeders produce the expected reference data and zero verified manufacturer templates', function () {
    $this->seed();

    expect(Occasion::count())->toBe(8)
        ->and(DesignFamily::count())->toBe(5)
        ->and(BookFormat::count())->toBe(6)
        ->and(ManufacturerTemplate::verified()->count())->toBe(0)
        ->and(ManufacturerTemplate::count())->toBe(0);

    expect(Occasion::orderBy('sort_order')->pluck('name')->all())->toBe([
        'Wedding', 'Engagement', 'Graduation', 'Birthday', 'Family', 'Travel', 'Corporate Portfolio', 'Baby/Newborn',
    ]);
    expect(DesignFamily::orderBy('sort_order')->pluck('name')->all())
        ->toBe(['Story', 'Minimal', 'Moments', 'Cinematic', 'Signature']);
    expect(BookFormat::orderBy('sort_order')->pluck('slug')->all())->toBe([
        '8x10-portrait', '8x12-portrait', '12x8-landscape', '10x12-portrait', '12x12-square', '12x18-portrait',
    ]);
});

test('seeders create the admin, demo photographers with branding, customers and the Yuppy brand', function () {
    $this->seed();

    expect(User::where('role', UserRole::Admin)->count())->toBe(1)
        ->and(User::where('role', UserRole::Customer)->count())->toBe(2);

    $photographers = User::where('role', UserRole::Photographer)->with('photographerProfile.brandingProfile')->get();
    expect($photographers)->toHaveCount(2);
    $photographers->each(fn (User $u) => expect($u->photographerProfile->brandingProfile)->not->toBeNull());

    $yuppy = BrandingProfile::yuppy();
    expect($yuppy)->not->toBeNull()->and($yuppy->hasLogo())->toBeTrue();
    Storage::disk('private')->assertExists($yuppy->logo_path);
});

test('seeders are idempotent', function () {
    $this->seed();
    $this->seed();

    expect(Occasion::count())->toBe(8)
        ->and(User::count())->toBe(5)
        ->and(BrandingProfile::where('is_yuppy', true)->count())->toBe(1);
});

test('manufacturer templates default to unverified with empty measurements', function () {
    $template = ManufacturerTemplate::factory()->create()->fresh();

    expect($template->is_verified)->toBeFalse()
        ->and($template->trim_width_mm)->toBeNull()
        ->and($template->bleed_mm)->toBeNull()
        ->and($template->spine_width_mm)->toBeNull();
});
