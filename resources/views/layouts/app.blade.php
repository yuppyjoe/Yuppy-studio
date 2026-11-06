<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name', 'Yuppy Studio') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @php($user = auth()->user())

        <div x-data="{ sidebarOpen: false }" x-on:keydown.escape.window="sidebarOpen = false" class="min-h-screen">
            {{-- Mobile backdrop --}}
            <div x-show="sidebarOpen" x-cloak x-transition.opacity
                 class="fixed inset-0 z-30 bg-ink/50 lg:hidden" x-on:click="sidebarOpen = false"></div>

            {{-- Sidebar --}}
            <aside
                class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-ink text-ivory-200 transition-transform duration-200 lg:w-64 lg:translate-x-0"
                :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
                aria-label="{{ __('Main navigation') }}"
            >
                <div class="flex h-20 items-center justify-between px-6">
                    <a href="{{ route('dashboard') }}" wire:navigate class="focus-ring rounded text-ivory-50 text-[15px]">
                        <x-application-logo />
                    </a>
                    <button type="button" class="focus-ring rounded p-1 text-grey-400 hover:text-ivory-50 lg:hidden"
                            x-on:click="sidebarOpen = false" aria-label="{{ __('Close menu') }}">
                        <x-ui.icon name="x" />
                    </button>
                </div>

                <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4">
                    <p class="eyebrow mb-3 px-3 !text-grey-500">{{ __('Menu') }}</p>
                    @foreach (\App\Support\Navigation::for($user) as $item)
                        @php($active = request()->routeIs($item['active']))
                        <a href="{{ route($item['route']) }}" wire:navigate
                           @class([
                               'focus-ring relative flex items-center gap-3 rounded-md px-3 py-2.5 text-sm transition',
                               'bg-charcoal-soft text-ivory-50' => $active,
                               'text-grey-300 hover:bg-charcoal hover:text-ivory-50' => ! $active,
                           ])
                           @if ($active) aria-current="page" @endif>
                            @if ($active)
                                <span class="absolute inset-y-2 left-0 w-0.5 rounded-full bg-champagne-500"></span>
                            @endif
                            <x-ui.icon :name="$item['icon']" @class(['h-[18px] w-[18px]', 'text-champagne-400' => $active]) />
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                {{-- Signed-in account: who you are; click to open account actions upwards --}}
                @php($onAccount = request()->routeIs('profile'))
                <div class="border-t border-charcoal-muted/70 p-3"
                     x-data="{ accountOpen: false }"
                     x-on:keydown.escape="accountOpen = false">
                    <p class="eyebrow mb-2 px-3 pt-1 !text-grey-500">{{ __('Your account') }}</p>

                    <div class="relative" x-on:click.outside="accountOpen = false">
                        {{-- Actions panel: opens above the account row --}}
                        <div id="account-actions"
                             x-show="accountOpen" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="translate-y-1 opacity-0"
                             x-transition:enter-end="translate-y-0 opacity-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="translate-y-1 opacity-0"
                             class="absolute inset-x-0 bottom-full mb-2 space-y-0.5 rounded-lg border border-charcoal-muted bg-charcoal p-1.5 shadow-lift">
                            <a href="{{ route('profile') }}" wire:navigate
                               @class([
                                   'focus-ring relative flex items-center gap-3 rounded-md px-3 py-2 text-sm transition',
                                   'bg-charcoal-soft text-ivory-50' => $onAccount,
                                   'text-grey-300 hover:bg-charcoal-soft hover:text-ivory-50' => ! $onAccount,
                               ])
                               @if ($onAccount) aria-current="page" @endif>
                                @if ($onAccount)
                                    <span class="absolute inset-y-2 left-0 w-0.5 rounded-full bg-champagne-500"></span>
                                @endif
                                <x-ui.icon name="user" @class(['h-[18px] w-[18px]', 'text-champagne-400' => $onAccount]) />
                                {{ __('Account settings') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="focus-ring flex w-full items-center gap-3 rounded-md px-3 py-2 text-left text-sm text-grey-300 transition hover:bg-charcoal-soft hover:text-ivory-50">
                                    <x-ui.icon name="logout" class="h-[18px] w-[18px]" />
                                    {{ __('Log out') }}
                                </button>
                            </form>
                        </div>

                        <button type="button"
                                class="focus-ring flex w-full items-center gap-3 rounded-md px-3 py-2 text-left transition hover:bg-charcoal"
                                :class="accountOpen && 'bg-charcoal'"
                                x-on:click="accountOpen = ! accountOpen"
                                :aria-expanded="accountOpen.toString()"
                                aria-controls="account-actions">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-champagne-500/15 font-serif text-lg text-champagne-300">
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm text-ivory-50">{{ $user->name }}</span>
                                <span class="block truncate text-xs text-champagne-400">{{ $user->role->label() }}</span>
                            </span>
                            <x-ui.icon name="chevron-right" class="h-4 w-4 shrink-0 text-grey-400 transition-transform duration-200"
                                       x-bind:class="accountOpen && '-rotate-90'" />
                            <span class="sr-only">{{ __('Show account actions') }}</span>
                        </button>
                    </div>
                </div>
            </aside>

            {{-- Main column --}}
            <div class="lg:pl-64">
                {{-- Mobile-only top bar: account controls live in the sidebar --}}
                <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-grey-200/70 bg-ivory-50/90 px-4 backdrop-blur sm:px-6 lg:hidden">
                    <button type="button" class="focus-ring -ml-1 rounded p-1.5 text-charcoal"
                            x-on:click="sidebarOpen = true" aria-label="{{ __('Open menu') }}">
                        <x-ui.icon name="menu" />
                    </button>

                    <x-application-logo class="text-[13px] text-ink" :tagline="null" />

                    <x-ui.badge tone="gold" class="ml-auto">{{ $user->role->label() }}</x-ui.badge>
                </header>

                <main class="mx-auto max-w-shell px-4 py-10 sm:px-6 lg:px-10 lg:py-14">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-ui.toast />
    </body>
</html>
