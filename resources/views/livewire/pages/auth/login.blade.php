<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <p class="eyebrow mb-3">{{ __('Welcome back') }}</p>
    <h1 class="text-4xl">{{ __('Sign in to your studio') }}</h1>
    <p class="mt-2 text-sm text-grey-500">
        {{ __('New to Yuppy?') }} <a href="{{ route('register') }}" wire:navigate class="link">{{ __('Create an account') }}</a>
    </p>

    <x-auth-session-status class="mt-8" :status="session('status')" />

    <form wire:submit="login" class="mt-8 space-y-5">
        <x-ui.input name="form.email" id="email" :label="__('Email')" type="email" wire:model="form.email" required autofocus autocomplete="username" />
        <x-ui.input name="form.password" id="password" :label="__('Password')" type="password" wire:model="form.password" required autocomplete="current-password" />

        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center gap-2 text-sm text-grey-600">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-grey-300 text-ink focus:ring-champagne-500" name="remember">
                {{ __('Remember me') }}
            </label>
            @if (Route::has('password.request'))
                <a class="link text-sm" href="{{ route('password.request') }}" wire:navigate>{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <x-ui.button class="w-full" size="lg" wire:loading.attr="disabled">{{ __('Sign in') }}</x-ui.button>
    </form>
</div>
