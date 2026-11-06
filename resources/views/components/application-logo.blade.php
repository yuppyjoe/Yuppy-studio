{{-- Yuppy wordmark. Pass class for size/colour; uses currentColor. --}}
@props(['tagline' => 'Studio'])

<span {{ $attributes->merge(['class' => 'inline-flex items-baseline gap-2 leading-none']) }}>
    <span class="font-serif text-[1.65em] font-semibold tracking-tight">Yuppy</span>
    @if ($tagline)
        <span class="text-[0.62em] font-medium uppercase tracking-eyebrow text-champagne-500">{{ $tagline }}</span>
    @endif
</span>
