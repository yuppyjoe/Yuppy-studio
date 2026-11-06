<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

beforeEach(function () {
    Storage::fake('private');
    $this->user = photographer();
    $this->actingAs($this->user);
});

function studioForm()
{
    return Volt::test('pages.studio.profile')
        ->set('branding.brand_name', 'Test Studio')
        ->set('branding.email', 'studio@example.com')
        ->set('branding.phone', '+254 700 000000');
}

test('a valid logo is stored privately and served only through the policy route', function () {
    studioForm()
        ->set('branding.logo', UploadedFile::fake()->image('logo.png', 300, 300))
        ->call('saveBranding')
        ->assertHasNoErrors();

    $branding = $this->user->photographerProfile->brandingProfile()->sole();

    expect($branding->logo_path)->toStartWith('branding/'.$branding->id.'/')
        ->and($branding->logo_mime)->toBe('image/png');
    Storage::disk('private')->assertExists($branding->logo_path);

    $this->get(route('files.branding.logo', $branding))->assertOk();
});

test('logo upload rejects non-image files', function () {
    studioForm()
        ->set('branding.logo', UploadedFile::fake()->create('logo.pdf', 50, 'application/pdf'))
        ->call('saveBranding')
        ->assertHasErrors('branding.logo');

    expect($this->user->photographerProfile->brandingProfile)->toBeNull();
});

test('logo upload rejects SVG, including SVG carrying scripts', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><script>alert(1)</script></svg>';

    studioForm()
        ->set('branding.logo', UploadedFile::fake()->createWithContent('logo.svg', $svg))
        ->call('saveBranding')
        ->assertHasErrors('branding.logo');
});

test('logo upload rejects a script disguised with an image extension', function () {
    studioForm()
        ->set('branding.logo', UploadedFile::fake()->createWithContent('logo.png', '<?php echo "hi"; ?>'))
        ->call('saveBranding')
        ->assertHasErrors('branding.logo');
});

test('logo upload rejects oversize files', function () {
    studioForm()
        ->set('branding.logo', UploadedFile::fake()->image('huge.png', 400, 400)->size(3000))
        ->call('saveBranding')
        ->assertHasErrors(['branding.logo' => 'max']);
});

test('replacing a logo deletes the old file and removing it clears it', function () {
    studioForm()->set('branding.logo', UploadedFile::fake()->image('one.png', 200, 200))->call('saveBranding');
    $first = $this->user->photographerProfile->brandingProfile()->sole()->logo_path;

    studioForm()->set('branding.logo', UploadedFile::fake()->image('two.jpg', 200, 200))->call('saveBranding');
    $branding = $this->user->photographerProfile->brandingProfile()->sole();

    Storage::disk('private')->assertMissing($first);
    Storage::disk('private')->assertExists($branding->logo_path);

    Volt::test('pages.studio.profile')->call('removeLogo');

    Storage::disk('private')->assertMissing($branding->logo_path);
    expect($branding->fresh()->logo_path)->toBeNull();
});

test('no logo is ever written to the public disk', function () {
    Storage::fake('public');

    studioForm()->set('branding.logo', UploadedFile::fake()->image('logo.png', 200, 200))->call('saveBranding');

    expect(Storage::disk('public')->allFiles())->toBe([]);
});
