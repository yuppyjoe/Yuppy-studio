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
                    <p class="eyebrow mb-3 px-3 !text-grey-500">{{ $user->role->label() }}</p>
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

                <div class="border-t border-charcoal-muted/70 p-4">
                    <a href="{{ route('profile') }}" wire:navigate class="focus-ring flex items-center gap-3 rounded-md p-2 transition hover:bg-charcoal">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-champagne-500/15 font-serif text-lg text-champagne-300">
                            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm text-ivory-50">{{ $user->name }}</span>
                            <span class="block truncate text-xs text-grey-400">{{ $user->email }}</span>
                        </span>
                    </a>
                </div>
            </aside>

            {{-- Main column --}}
            <div class="lg:pl-64">
                <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-grey-200/70 bg-ivory-50/90 px-4 backdrop-blur sm:px-6 lg:px-10">
                    <button type="button" class="focus-ring -ml-1 rounded p-1.5 text-charcoal lg:hidden"
                            x-on:click="sidebarOpen = true" aria-label="{{ __('Open menu') }}">
                        <x-ui.icon name="menu" />
                    </button>

                    <x-application-logo class="text-[13px] text-ink lg:hidden" :tagline="null" />

                    <div class="ml-auto flex items-center gap-2">
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger">
                                <button type="button" class="focus-ring flex items-center gap-2 rounded-md px-2.5 py-1.5 text-sm text-charcoal transition hover:bg-ivory-200">
                                    <span class="hidden sm:inline">{{ $user->name }}</span>
                                    <x-ui.badge tone="gold">{{ $user->role->label() }}</x-ui.badge>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile')" wire:navigate>
                                    <x-ui.icon name="user" class="h-4 w-4 text-grey-500" /> {{ __('Account settings') }}
                                </x-dropdown-link>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2 text-start text-sm text-charcoal transition hover:bg-ivory-100 focus:bg-ivory-100 focus:outline-none">
                                        <x-ui.icon name="logout" class="h-4 w-4 text-grey-500" /> {{ __('Log out') }}
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <main class="mx-auto max-w-shell px-4 py-10 sm:px-6 lg:px-10 lg:py-14">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-ui.toast />
    </body>
</html>
