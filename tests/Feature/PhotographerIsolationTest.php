<?php

use App\Models\BrandingProfile;
use App\Models\Customer;
use App\Models\PhotobookProject;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

beforeEach(function () {
    Storage::fake('private');

    $this->a = photographer(withBranding: true);
    $this->b = photographer(withBranding: true);

    $this->bProfile = $this->b->photographerProfile;
    $this->bBranding = $this->bProfile->brandingProfile;
    $this->bCustomer = Customer::factory()->for($this->bProfile)->create(['name' => 'Bravo Client']);
    $this->bProject = PhotobookProject::factory()->forPhotographer($this->bProfile)->create(['title' => 'Bravo Wedding Book']);

    // Give B a real logo on the private disk.
    $this->bBranding->forceFill(['logo_path' => 'branding/'.$this->bBranding->id.'/logo.png', 'logo_mime' => 'image/png'])->save();
    Storage::disk('private')->put($this->bBranding->logo_path, UploadedFile::fake()->image('logo.png', 100, 100)->getContent());

    $this->aCustomer = Customer::factory()->for($this->a->photographerProfile)->create(['name' => 'Alpha Client']);
});

test('photographer A cannot view or edit B\'s profile or branding (policies)', function () {
    expect($this->a->can('view', $this->bProfile))->toBeFalse()
        ->and($this->a->can('update', $this->bProfile))->toBeFalse()
        ->and($this->a->can('view', $this->bBranding))->toBeFalse()
        ->and($this->a->can('update', $this->bBranding))->toBeFalse()
        ->and($this->a->can('viewLogo', $this->bBranding))->toBeFalse();
});

test('photographer A\'s studio page only ever loads A\'s own records', function () {
    $this->actingAs($this->a)
        ->get(route('studio.profile'))
        ->assertOk()
        ->assertSee($this->a->photographerProfile->studio_name)
        ->assertDontSee($this->bProfile->studio_name)
        ->assertDontSee($this->bBranding->brand_name);
});

test('photographer A cannot download B\'s logo', function () {
    $this->actingAs($this->a)
        ->get(route('files.branding.logo', $this->bBranding))
        ->assertForbidden();

    // ...while B can, with safe headers.
    $this->actingAs($this->b)
        ->get(route('files.branding.logo', $this->bBranding))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Content-Type', 'image/png');
});

test('guests cannot download any logo', function () {
    $this->get(route('files.branding.logo', $this->bBranding))->assertRedirect(route('login'));
});

test('photographer A cannot view or edit B\'s customers', function () {
    expect($this->a->can('view', $this->bCustomer))->toBeFalse()
        ->and($this->a->can('update', $this->bCustomer))->toBeFalse()
        ->and($this->a->can('delete', $this->bCustomer))->toBeFalse();

    $this->actingAs($this->a)
        ->get(route('studio.customers.edit', $this->bCustomer))
        ->assertForbidden();
});

test('photographer A\'s client list never includes B\'s customers', function () {
    $this->actingAs($this->a)
        ->get(route('studio.customers.index'))
        ->assertOk()
        ->assertSee('Alpha Client')
        ->assertDontSee('Bravo Client');

    expect(Customer::visibleTo($this->a)->pluck('id')->all())->toBe([$this->aCustomer->id]);
});

test('photographer A cannot delete B\'s customer through the component', function () {
    $this->actingAs($this->a);

    expect(fn () => Volt::test('pages.studio.customers.index')->call('confirmDelete', $this->bCustomer->id))
        ->toThrow(ModelNotFoundException::class);

    expect($this->bCustomer->fresh())->not->toBeNull();
});

test('a client created by A is always owned by A', function () {
    $this->actingAs($this->a);

    Volt::test('pages.studio.customers.form')
        ->set('name', 'New Client')
        ->call('save')
        ->assertHasNoErrors();

    expect(Customer::where('name', 'New Client')->sole()->photographer_profile_id)
        ->toBe($this->a->photographerProfile->id);
});

test('photographer A cannot view B\'s projects and does not see them listed', function () {
    expect($this->a->can('view', $this->bProject))->toBeFalse()
        ->and($this->a->can('update', $this->bProject))->toBeFalse();

    $this->actingAs($this->a)
        ->get(route('studio.projects.show', $this->bProject))
        ->assertForbidden();

    $this->actingAs($this->a)
        ->get(route('studio.projects.index'))
        ->assertOk()
        ->assertDontSee('Bravo Wedding Book');

    $this->actingAs($this->b)
        ->get(route('studio.projects.show', $this->bProject))
        ->assertOk()
        ->assertSee('Bravo Wedding Book');
});

test('customers only see their own projects', function () {
    $mine = customerUser();
    $other = customerUser();
    $myProject = PhotobookProject::factory()->forCustomer($mine->customer)->create(['title' => 'My Graduation']);
    $theirProject = PhotobookProject::factory()->forCustomer($other->customer)->create(['title' => 'Their Holiday']);

    $this->actingAs($mine)
        ->get(route('customer.dashboard'))
        ->assertSee('My Graduation')
        ->assertDontSee('Their Holiday');

    $this->actingAs($mine)->get(route('customer.projects.show', $myProject))->assertOk();
    $this->actingAs($mine)->get(route('customer.projects.show', $theirProject))->assertForbidden();
});

test('admins can see every studio\'s records', function () {
    $admin = admin();

    expect($admin->can('view', $this->bProfile))->toBeTrue()
        ->and($admin->can('view', $this->bCustomer))->toBeTrue()
        ->and($admin->can('viewLogo', $this->bBranding))->toBeTrue()
        ->and(Customer::visibleTo($admin)->count())->toBe(2);

    $this->actingAs($admin)
        ->get(route('admin.photographers'))
        ->assertSee($this->bProfile->studio_name)
        ->assertSee($this->a->photographerProfile->studio_name);
});

test('admins cannot edit photographers\' studio records in phase 1', function () {
    $admin = admin();

    expect($admin->can('update', $this->bProfile))->toBeFalse()
        ->and($admin->can('update', $this->bBranding))->toBeFalse()
        ->and($admin->can('update', BrandingProfile::factory()->yuppy()->create()))->toBeTrue();
});
