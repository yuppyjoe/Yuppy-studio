@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-charcoal']) }}>
    {{ $value ?? $slot }}
</label>
