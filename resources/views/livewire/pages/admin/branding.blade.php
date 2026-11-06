<?php

use App\Livewire\Forms\BrandingForm;
use App\Models\BrandingProfile;
use App\Services\AuditLogger;
use App\Services\LogoStorage;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Title('Yuppy branding')] class extends Component
{
    use WithFileUploads;

    public BrandingForm $branding;

    public function mount(): void
    {
        $this->authorize('update', $this->yuppy());
        $this->branding->fillFrom($this->yuppy());
    }

    /** The single house brand (created on first visit if the seeder has not run). */
    protected function yuppy(): BrandingProfile
    {
        return BrandingProfile::yuppy()
            ?? (new BrandingProfile)->forceFill(['is_yuppy' => true, 'photographer_profile_id' => null, 'brand_name' => 'Yuppy']);
    }

    public function with(): array
    {
        return ['brandingProfile' => $this->yuppy()];
    }

    public function updatedBrandingLogo(): void
    {
        $this->branding->validateOnly('logo');
    }

    public function saveBranding(): void
    {
        $profile = $this->yuppy();
        $this->authorize('update', $profile);

        $this->branding->save($profile, 'branding_profile.updated');

        $this->dispatch('toast', message: __('Yuppy branding saved.'));
    }

    public function removeLogo(LogoStorage $logos, AuditLogger $audit): void
    {
        $profile = $this->yuppy();
        abort_unless($profile->exists, 404);
        $this->authorize('update', $profile);

        $previous = $profile->logo_path;
        $logos->delete($profile);
        $audit->log('branding_profile.logo_updated', $profile, ['logo_path' => $previous], ['logo_path' => null]);

        $this->dispatch('toast', message: __('Logo removed.'));
    }
}; ?>

<div>
    <x-ui.page-header :eyebrow="__('Yuppy administration')" :title="__('Yuppy branding')"
        :description="__('The house brand printed on books Yuppy makes directly for customers. It is never applied to photographers\' books.')" />

    <form wire:submit="saveBranding" class="max-w-4xl">
        <x-ui.card :title="__('House brand')">
            <x-branding-fields model="branding" :branding="$brandingProfile" :upload="$branding->logo" />
            <x-slot name="footer">
                <x-ui.button wire:loading.attr="disabled" wire:target="saveBranding,branding.logo">{{ __('Save branding') }}</x-ui.button>
            </x-slot>
        </x-ui.card>
    </form>
</div>
