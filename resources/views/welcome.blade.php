<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Yuppy Studio') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-ivory-50">
        <div class="mx-auto flex min-h-screen max-w-shell flex-col px-6 sm:px-10">
            <header class="flex items-center justify-between py-8">
                <a href="/" class="focus-ring rounded text-ink"><x-application-logo /></a>
                <nav class="flex items-center gap-2">
                    @auth
                        <x-ui.button :href="route('dashboard')">{{ __('Open dashboard') }}</x-ui.button>
                    @else
                        <x-ui.button variant="ghost" :href="route('login')">{{ __('Sign in') }}</x-ui.button>
                        <x-ui.button :href="route('register')">{{ __('Create account') }}</x-ui.button>
                    @endauth
                </nav>
            </header>

            <main class="grid flex-1 items-center gap-16 py-16 lg:grid-cols-2">
                <div>
                    <p class="eyebrow mb-6">{{ __('AI photobook designer') }}</p>
                    <h1 class="text-5xl leading-[1.05] sm:text-6xl lg:text-7xl">
                        {{ __('Books worthy of') }}<br><em class="font-medium text-charcoal-muted">{{ __('the moments in them.') }}</em>
                    </h1>
                    <p class="mt-8 max-w-md text-lg text-grey-600">
                        {{ __('Yuppy helps photographers design beautiful photobooks for their clients under their own studio brand, and helps families keep their stories in print.') }}
                    </p>
                    <div class="mt-10 flex flex-wrap gap-3">
                        @guest
                            <x-ui.button size="lg" :href="route('register')">{{ __('Start as a photographer') }}</x-ui.button>
                            <x-ui.button size="lg" variant="secondary" :href="route('login')">{{ __('Sign in') }}</x-ui.button>
                        @endguest
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-md" aria-hidden="true">
                    <div class="absolute -left-6 top-10 h-full w-full rotate-[-4deg] rounded-sm bg-ivory-300"></div>
                    <div class="relative flex aspect-[4/5] flex-col justify-between rounded-sm bg-ink p-10 text-ivory-100 shadow-lift">
                        <span class="h-px w-10 bg-champagne-500"></span>
                        <div>
                            <p class="eyebrow !text-champagne-400">{{ __('Wedding · 12x12') }}</p>
                            <p class="mt-3 font-serif text-4xl italic text-ivory-50">Wanjiru &amp; Otieno</p>
                        </div>
                        <p class="text-xs uppercase tracking-eyebrow text-grey-500">{{ __('Your studio name here') }}</p>
                    </div>
                </div>
            </main>

            <footer class="border-t border-grey-200 py-8 text-xs text-grey-500">&copy; {{ date('Y') }} Yuppy Enterprises</footer>
        </div>
    </body>
</html>
