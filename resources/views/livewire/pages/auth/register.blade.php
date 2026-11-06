<?php

use App\Actions\RegisterUser;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $role = 'photographer';
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function register(RegisterUser $registerUser): void
    {
        $validated = $this->validate([
            // Only self-registrable roles are accepted; "admin" is rejected here.
            'role' => ['required', Rule::in(array_map(fn (UserRole $r) => $r->value, UserRole::selfRegistrable()))],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $registerUser->handle(
            ['name' => $validated['name'], 'email' => $validated['email'], 'password' => $validated['password']],
            UserRole::from($validated['role']),
        );

        event(new Registered($user));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <p class="eyebrow mb-3">{{ __('Create your account') }}</p>
    <h1 class="text-4xl">{{ __('Begin with Yuppy') }}</h1>
    <p class="mt-2 text-sm text-grey-500">
        {{ __('Already have an account?') }} <a href="{{ route('login') }}" wire:navigate class="link">{{ __('Sign in') }}</a>
    </p>

    <form wire:submit="register" class="mt-8 space-y-5">
        <fieldset>
            <legend class="mb-2 text-sm font-medium text-charcoal">{{ __('I am joining as') }}</legend>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ([
                    'photographer' => [__('A photographer'), __('Design books for my clients under my own studio brand.')],
                    'customer' => [__('A customer'), __('Create a photobook of my own moments.')],
                ] as $value => [$title, $text])
                    <label @class([
                        'relative flex cursor-pointer flex-col rounded-lg border p-4 transition focus-within:ring-2 focus-within:ring-champagne-500',
                        'border-ink bg-white shadow-soft' => $role === $value,
                        'border-grey-200 bg-white/60 hover:border-grey-300' => $role !== $value,
                    ])>
                        <input type="radio" wire:model.live="role" name="role" value="{{ $value }}" class="sr-only">
                        <span class="flex items-center justify-between">
                            <span class="font-serif text-xl text-ink">{{ $title }}</span>
                            @if ($role === $value)
                                <x-ui.icon name="check" class="h-4 w-4 text-champagne-600" />
                            @endif
                        </span>
                        <span class="mt-1 text-xs text-grey-500">{{ $text }}</span>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </fieldset>

        <x-ui.input name="name" id="name" :label="__('Full name')" wire:model="name" required autofocus autocomplete="name" />
        <x-ui.input name="email" id="email" :label="__('Email')" type="email" wire:model="email" required autocomplete="username" />
        <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.input name="password" id="password" :label="__('Password')" type="password" wire:model="password" required autocomplete="new-password" />
            <x-ui.input name="password_confirmation" id="password_confirmation" :label="__('Confirm password')" type="password" wire:model="password_confirmation" required autocomplete="new-password" />
        </div>

        <x-ui.button class="w-full" size="lg" wire:loading.attr="disabled">{{ __('Create account') }}</x-ui.button>
    </form>
</div>
