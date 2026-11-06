@props([
    'title' => null,
    'description' => null,
    'padded' => true,
])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-grey-200/80 bg-white shadow-soft']) }}>
    @if ($title || isset($actions))
        <header class="flex flex-wrap items-start justify-between gap-4 border-b border-grey-100 px-6 py-5 sm:px-8">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="text-2xl">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-1 max-w-prose text-sm text-grey-500">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div @class(['px-6 py-6 sm:px-8' => $padded])>
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="flex flex-wrap items-center justify-end gap-3 rounded-b-lg border-t border-grey-100 bg-ivory-50 px-6 py-4 sm:px-8">
            {{ $footer }}
        </footer>
    @endisset
</section>
