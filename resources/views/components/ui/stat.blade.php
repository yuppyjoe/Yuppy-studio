@props([
    'label',
    'value',
    'href' => null,
    'hint' => null,
])

@php($tag = $href ? 'a' : 'div')

<{{ $tag }} @if ($href) href="{{ $href }}" wire:navigate @endif
    {{ $attributes->merge(['class' => 'group block rounded-lg border border-grey-200/80 bg-white p-6 shadow-soft transition'.($href ? ' focus-ring hover:border-champagne-300' : '')]) }}>
    <p class="eyebrow">{{ $label }}</p>
    <p class="mt-3 font-serif text-5xl leading-none text-ink">{{ $value }}</p>
    @if ($hint)
        <p class="mt-3 text-xs text-grey-500">{{ $hint }}</p>
    @endif
</{{ $tag }}>
