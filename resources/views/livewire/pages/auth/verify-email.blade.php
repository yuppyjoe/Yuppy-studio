<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <p class="eyebrow mb-3">{{ __('One more step') }}</p>
    <h1 class="text-4xl">{{ __('Verify your email') }}</h1>
    <p class="mt-3 text-sm text-grey-500">
        {{ __('We sent a verification link to your email address. Click it to activate your account. If it has not arrived, we can send another.') }}
    </p>

    @if (session('status') == 'verification-link-sent')
        <x-auth-session-status class="mt-6" :status="__('A new verification link has been sent to your email address.')" />
    @endif

    <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
        <x-ui.button type="button" wire:click="sendVerification" wire:loading.attr="disabled">{{ __('Resend verification email') }}</x-ui.button>
        <x-ui.button type="button" variant="ghost" wire:click="logout">{{ __('Log out') }}</x-ui.button>
    </div>
</div>
