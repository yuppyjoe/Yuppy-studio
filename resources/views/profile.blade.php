<x-app-layout>
    <x-slot name="title">{{ __('Account settings') }}</x-slot>

    <x-ui.page-header :eyebrow="__('Your account')" :title="__('Account settings')"
        :description="__('Your sign-in details. Studio and branding details are managed separately.')" />

    <div class="max-w-3xl space-y-8">
        <x-ui.card><livewire:profile.update-profile-information-form /></x-ui.card>
        <x-ui.card><livewire:profile.update-password-form /></x-ui.card>
        <x-ui.card><livewire:profile.delete-user-form /></x-ui.card>
    </div>
</x-app-layout>
