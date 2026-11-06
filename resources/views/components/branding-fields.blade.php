{{--
    Branding form fields bound to a BrandingForm object.
    Props: model (the form property name), branding (BrandingProfile), upload (TemporaryUploadedFile|null)
--}}
@props(['model', 'branding', 'upload' => null])

@php
    $preview = $upload?->isPreviewable() ? $upload->temporaryUrl() : $branding->logoUrl();
@endphp

<div class="space-y-6">
    <div class="grid gap-5 sm:grid-cols-2">
        <x-ui.input :name="$model.'.brand_name'" :label="__('Brand name')" wire:model="{{ $model }}.brand_name" :hint="__('As it should appear in printed books.')" required />
        <x-ui.input :name="$model.'.tagline'" :label="__('Tagline')" wire:model="{{ $model }}.tagline" optional />
        <x-ui.input :name="$model.'.email'" :label="__('Contact email')" type="email" wire:model="{{ $model }}.email" required />
        <x-ui.input :name="$model.'.phone'" :label="__('Contact phone')" type="tel" wire:model="{{ $model }}.phone" required />
        <x-ui.input :name="$model.'.website'" :label="__('Website')" type="url" placeholder="https://" wire:model="{{ $model }}.website" optional class="sm:col-span-2" />
    </div>

    <div>
        <p class="mb-3 text-sm font-medium text-charcoal">{{ __('Social profiles') }} <span class="ml-1 text-xs font-normal text-grey-400">{{ __('Optional') }}</span></p>
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach (\App\Models\BrandingProfile::SOCIAL_NETWORKS as $key => $network)
                <x-ui.input :name="$model.'.social_links.'.$key" :label="$network" placeholder="@handle or link" wire:model="{{ $model }}.social_links.{{ $key }}" />
            @endforeach
        </div>
    </div>

    <x-ui.file-upload
        :name="$model.'.logo'"
        :label="__('Logo')"
        wire:model="{{ $model }}.logo"
        accept="image/png,image/jpeg,image/webp"
        :preview="$preview"
        :hint="__('PNG, JPG or WebP, at least 64×64 px, up to :size MB. SVG is not accepted.', ['size' => \App\Services\LogoStorage::MAX_KILOBYTES / 1024])"
    >
        @if ($upload)
            <p class="text-xs text-grey-600">{{ __('New logo selected. It will be saved with your changes.') }}</p>
        @elseif ($branding->hasLogo())
            <button type="button" class="text-xs font-medium text-danger hover:underline" x-on:click="$dispatch('open-modal', 'remove-logo')">{{ __('Remove logo') }}</button>
        @endif
    </x-ui.file-upload>
</div>

@if ($branding->hasLogo())
    <x-ui.modal name="remove-logo" :title="__('Remove logo?')">
        {{ __('The logo will be deleted. You can upload a new one at any time.') }}
        <x-slot name="footer">
            <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-ui.button>
            <x-ui.button variant="danger" type="button" wire:click="removeLogo" x-on:click="$dispatch('close')">{{ __('Remove logo') }}</x-ui.button>
        </x-slot>
    </x-ui.modal>
@endif
