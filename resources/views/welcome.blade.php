<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Yuppy Studio') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-ivory-50">
        {{-- Full-width layout: content spans the whole viewport, edge to edge. --}}
        <div class="flex min-h-screen w-full flex-col">
            <header class="absolute inset-x-0 top-0 z-10 flex w-full items-center justify-between px-6 py-6 sm:px-10 lg:px-16">
                <a href="/" class="focus-ring rounded text-ink"><x-application-logo /></a>
                <nav class="flex items-center gap-2">
                    @auth
                        <x-ui.button :href="route('dashboard')" class="lg:bg-ivory-50 lg:text-ink lg:hover:bg-ivory-200">{{ __('Open dashboard') }}</x-ui.button>
                    @else
                        <x-ui.button variant="ghost" :href="route('login')" class="lg:text-ivory-100 lg:hover:bg-charcoal lg:hover:text-ivory-50">{{ __('Sign in') }}</x-ui.button>
                        <x-ui.button :href="route('register')" class="lg:bg-ivory-50 lg:text-ink lg:hover:bg-ivory-200">{{ __('Create account') }}</x-ui.button>
                    @endauth
                </nav>
            </header>

            <main class="grid w-full flex-1 lg:grid-cols-2">
                {{-- Left: message --}}
                <section class="flex flex-col justify-center px-6 pb-16 pt-32 sm:px-10 lg:px-16 lg:py-32 xl:px-24">
                    <p class="eyebrow mb-6">{{ __('AI photobook designer') }}</p>
                    <h1 class="text-5xl leading-[1.05] sm:text-6xl xl:text-7xl 2xl:text-8xl">
                        {{ __('Books worthy of') }}<br><em class="font-medium text-charcoal-muted">{{ __('the moments in them.') }}</em>
                    </h1>
                    <p class="mt-8 max-w-xl text-lg text-grey-600">
                        {{ __('Yuppy helps photographers design beautiful photobooks for their clients under their own studio brand, and helps families keep their stories in print.') }}
                    </p>
                    @guest
                        <div class="mt-10 flex flex-wrap gap-3">
                            <x-ui.button size="lg" :href="route('register')">{{ __('Start as a photographer') }}</x-ui.button>
                            <x-ui.button size="lg" variant="secondary" :href="route('login')">{{ __('Sign in') }}</x-ui.button>
                        </div>
                    @endguest

                    <dl class="mt-16 grid max-w-xl grid-cols-3 gap-6 border-t border-grey-200 pt-8">
                        @foreach ([[__('Occasions'), \App\Models\Occasion::active()->count()], [__('Design families'), \App\Models\DesignFamily::active()->count()], [__('Book formats'), \App\Models\BookFormat::active()->count()]] as [$label, $count])
                            <div>
                                <dt class="eyebrow">{{ $label }}</dt>
                                <dd class="mt-2 font-serif text-4xl lining-nums text-ink">{{ $count }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                {{-- Right: full-bleed showcase panel, edge to edge --}}
                <section class="relative flex min-h-[28rem] items-center justify-center overflow-hidden bg-ink px-6 py-20 sm:px-10 lg:min-h-screen" aria-hidden="true">
                    <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
                         style="background-image: radial-gradient(circle at 1px 1px, #FAF6EE 1px, transparent 0); background-size: 28px 28px;"></div>

                    <div class="relative w-full max-w-sm xl:max-w-md">
                        <div class="absolute -left-8 top-8 h-full w-full rotate-[-5deg] rounded-sm bg-charcoal-muted"></div>
                        <div class="absolute -left-4 top-4 h-full w-full rotate-[-2deg] rounded-sm bg-ivory-300"></div>
                        <div class="relative flex aspect-[4/5] flex-col justify-between rounded-sm bg-ivory-100 p-10 shadow-lift">
                            <span class="h-px w-10 bg-champagne-500"></span>
                            <div>
                                <p class="eyebrow !text-champagne-600">{{ __('Wedding · 12x12') }}</p>
                                <p class="mt-3 font-serif text-4xl italic text-ink xl:text-5xl">Wanjiru &amp; Otieno</p>
                            </div>
                            <p class="text-xs uppercase tracking-eyebrow text-grey-500">{{ __('Your studio name here') }}</p>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="flex w-full flex-wrap items-center justify-between gap-4 border-t border-grey-200 px-6 py-6 text-xs text-grey-500 sm:px-10 lg:px-16">
                <span>&copy; {{ date('Y') }} Yuppy Enterprises</span>
                <span>{{ __('Photobooks, beautifully made.') }}</span>
            </footer>
        </div>
    </body>
</html>
