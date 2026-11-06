@props([
    'eyebrow' => null,
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-10 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between']) }}>
    <div class="min-w-0">
        @if ($eyebrow)
            <p class="eyebrow mb-3">{{ $eyebrow }}</p>
        @endif
        <h1 class="text-4xl leading-tight sm:text-[2.75rem]">{{ $title }}</h1>
        @if ($description)
            <p class="mt-3 max-w-2xl text-grey-500">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-3">{{ $actions }}</div>
    @endisset
</div>
