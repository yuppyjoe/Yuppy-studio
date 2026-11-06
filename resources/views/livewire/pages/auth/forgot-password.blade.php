<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <p class="eyebrow mb-3">{{ __('Password reset') }}</p>
    <h1 class="text-4xl">{{ __('Forgot your password?') }}</h1>
    <p class="mt-2 text-sm text-grey-500">{{ __('Enter your email and we will send you a link to choose a new one.') }}</p>

    <x-auth-session-status class="mt-8" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="mt-8 space-y-5">
        <x-ui.input name="email" id="email" :label="__('Email')" type="email" wire:model="email" required autofocus />
        <x-ui.button class="w-full" size="lg" wire:loading.attr="disabled">{{ __('Email reset link') }}</x-ui.button>
        <p class="text-center text-sm"><a href="{{ route('login') }}" wire:navigate class="link">{{ __('Back to sign in') }}</a></p>
    </form>
</div>
