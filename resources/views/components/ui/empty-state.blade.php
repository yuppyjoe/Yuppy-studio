@props([
    'icon' => 'book',
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center px-6 py-14 text-center']) }}>
    <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-full border border-champagne-200 bg-champagne-50">
        <x-ui.icon :name="$icon" class="h-6 w-6 text-champagne-600" />
    </div>
    <h3 class="text-2xl">{{ $title }}</h3>
    @if ($description)
        <p class="mt-2 max-w-sm text-sm text-grey-500">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>
