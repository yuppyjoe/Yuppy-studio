<?php

use App\Livewire\Forms\BrandingForm;
use App\Models\BrandingProfile;
use App\Models\PhotographerProfile;
use App\Services\AuditLogger;
use App\Services\LogoStorage;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Title('Studio & branding')] class extends Component
{
    use WithFileUploads;

    public PhotographerProfile $profile;

    public string $studio_name = '';
    public string $phone = '';
    public string $email = '';
    public string $website = '';
    public string $city = '';
    public string $bio = '';

    public BrandingForm $branding;

    public function mount(): void
    {
        $this->profile = auth()->user()->photographerProfile()->firstOrFail();
        $this->authorize('update', $this->profile);

        $this->fill(array_map('strval', $this->profile->only(['studio_name', 'phone', 'email', 'website', 'city', 'bio'])));

        $this->branding->fillFrom($this->brandingProfile());
    }

    /**
     * The studio's branding profile, or an unsaved one pre-filled from the
     * studio profile until the photographer first saves the branding form.
     */
    protected function brandingProfile(): BrandingProfile
    {
        return $this->profile->brandingProfile()->first()
            ?? (new BrandingProfile)->forceFill([
                'photographer_profile_id' => $this->profile->id,
                'is_yuppy' => false,
                'brand_name' => $this->profile->studio_name,
                'email' => $this->profile->email,
                'phone' => $this->profile->phone,
            ]);
    }

    public function with(): array
    {
        return ['brandingProfile' => $this->brandingProfile()];
    }

    public function saveStudio(AuditLogger $audit): void
    {
        $this->authorize('update', $this->profile);

        $validated = $this->validate([
            'studio_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\-\s.]{6,40}$/'],
            'email' => ['required', 'email', 'max:255'],
            'website' => ['nullable', 'url:http,https', 'max:255'],
            'city' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ], ['phone.regex' => 'Enter a valid phone number (digits, spaces, +, - and brackets).']);

        $this->profile->fill(array_map(fn ($v) => $v === '' ? null : $v, $validated))->save();
        $audit->logChanges('photographer_profile.updated', $this->profile);

        $this->dispatch('toast', message: __('Studio profile saved.'));
    }

    public function updatedBrandingLogo(): void
    {
        $this->branding->validateOnly('logo');
    }

    public function saveBranding(): void
    {
        $brandingProfile = $this->brandingProfile();
        $this->authorize('update', $brandingProfile);

        $this->branding->save($brandingProfile, 'branding_profile.updated');

        $this->dispatch('toast', message: __('Branding saved.'));
    }

    public function removeLogo(LogoStorage $logos, AuditLogger $audit): void
    {
        $brandingProfile = $this->brandingProfile();
        abort_unless($brandingProfile->exists, 404);
        $this->authorize('update', $brandingProfile);

        $previous = $brandingProfile->logo_path;
        $logos->delete($brandingProfile);
        $audit->log('branding_profile.logo_updated', $brandingProfile, ['logo_path' => $previous], ['logo_path' => null]);

        $this->dispatch('toast', message: __('Logo removed.'));
    }
}; ?>

<div>
    <x-ui.page-header
        :eyebrow="__('Your studio')"
        :title="__('Studio & branding')"
        :description="__('Your studio details, and the brand that appears in the books you make for clients. Yuppy branding never appears on your books.')"
    />

    <div class="space-y-8">
        <form wire:submit="saveStudio">
            <x-ui.card :title="__('Studio profile')" :description="__('How Yuppy and your clients reach you.')">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.input name="studio_name" :label="__('Studio name')" wire:model="studio_name" required class="sm:col-span-2" />
                    <x-ui.input name="phone" :label="__('Phone')" type="tel" wire:model="phone" required />
                    <x-ui.input name="email" :label="__('Email')" type="email" wire:model="email" required />
                    <x-ui.input name="website" :label="__('Website')" type="url" placeholder="https://" wire:model="website" optional />
                    <x-ui.input name="city" :label="__('City')" wire:model="city" optional />
                    <x-ui.textarea name="bio" :label="__('About the studio')" wire:model="bio" optional rows="4" class="sm:col-span-2" />
                </div>
                <x-slot name="footer">
                    <x-ui.button wire:loading.attr="disabled" wire:target="saveStudio">{{ __('Save studio profile') }}</x-ui.button>
                </x-slot>
            </x-ui.card>
        </form>

        <form wire:submit="saveBranding">
            <x-ui.card :title="__('Book branding')" :description="__('Printed on the books you design. Only you and Yuppy administrators can see these details.')">
                <x-branding-fields model="branding" :branding="$brandingProfile" :upload="$branding->logo" />
                <x-slot name="footer">
                    <x-ui.button wire:loading.attr="disabled" wire:target="saveBranding,branding.logo">{{ __('Save branding') }}</x-ui.button>
                </x-slot>
            </x-ui.card>
        </form>
    </div>
</div>
