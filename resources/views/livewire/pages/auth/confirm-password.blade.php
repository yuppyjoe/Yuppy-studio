<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <p class="eyebrow mb-3">{{ __('Secure area') }}</p>
    <h1 class="text-4xl">{{ __('Confirm your password') }}</h1>
    <p class="mt-2 text-sm text-grey-500">{{ __('Please confirm your password before continuing.') }}</p>

    <form wire:submit="confirmPassword" class="mt-8 space-y-5">
        <x-ui.input name="password" id="password" :label="__('Password')" type="password" wire:model="password" required autocomplete="current-password" />
        <x-ui.button class="w-full" size="lg" wire:loading.attr="disabled">{{ __('Confirm') }}</x-ui.button>
    </form>
</div>
