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
        <div class="grid min-h-screen lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
            <aside class="relative hidden flex-col justify-between overflow-hidden bg-ink p-12 text-ivory-100 lg:flex">
                <a href="/" wire:navigate class="focus-ring self-start rounded text-ivory-50"><x-application-logo /></a>

                <div class="relative max-w-md">
                    <span class="mb-8 block h-px w-12 bg-champagne-500"></span>
                    <p class="font-serif text-[2.6rem] font-medium italic leading-[1.15] text-ivory-50">
                        Every photograph deserves a book worthy of the moment.
                    </p>
                    <p class="mt-6 text-sm text-grey-400">Designed photobooks for photographers and the people they photograph.</p>
                </div>

                <p class="eyebrow !text-grey-500">&copy; {{ date('Y') }} Yuppy Enterprises</p>
            </aside>

            <main class="flex flex-col px-6 py-10 sm:px-12">
                <a href="/" wire:navigate class="focus-ring mb-12 self-start rounded text-ink lg:hidden"><x-application-logo /></a>
                <div class="mx-auto my-auto w-full max-w-md">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <x-ui.toast />
    </body>
</html>
