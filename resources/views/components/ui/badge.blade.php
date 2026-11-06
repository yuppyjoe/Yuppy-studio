@props(['tone' => 'neutral'])

@php
    $tones = [
        'neutral' => 'bg-grey-100 text-grey-700',
        'gold' => 'bg-champagne-100 text-champagne-700',
        'dark' => 'bg-charcoal text-ivory-100',
        'success' => 'bg-success-soft text-success',
        'danger' => 'bg-danger-soft text-danger',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-medium uppercase tracking-wider '.($tones[$tone] ?? $tones['neutral'])]) }}>{{ $slot }}</span>
