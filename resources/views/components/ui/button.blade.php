@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'submit',
])

@php
    $base = 'focus-ring inline-flex items-center justify-center gap-2 rounded-md font-medium transition duration-150 disabled:cursor-not-allowed disabled:opacity-50 whitespace-nowrap';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-[13px]',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-[15px]',
    ];

    $variants = [
        'primary' => 'bg-ink text-ivory-50 hover:bg-charcoal-muted',
        'secondary' => 'border border-grey-200 bg-white text-ink hover:border-grey-300 hover:bg-ivory-100',
        'ghost' => 'text-grey-700 hover:bg-ivory-200 hover:text-ink',
        'danger' => 'bg-danger text-white hover:bg-danger/90',
        'danger-ghost' => 'text-danger hover:bg-danger-soft',
    ];

    $classes = $base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
