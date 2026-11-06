@props([
    'label' => null,
    'name',
    'type' => 'text',
    'hint' => null,
    'optional' => false,
])

@php
    $id = $attributes->get('id', 'field-'.str_replace('.', '-', $name));
    $invalid = $errors->has($name);
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <label for="{{ $id }}" class="flex items-baseline justify-between text-sm font-medium text-charcoal">
            <span>{{ $label }}</span>
            @if ($optional)
                <span class="text-xs font-normal text-grey-400">{{ __('Optional') }}</span>
            @endif
        </label>
    @endif

    <input
        id="{{ $id }}"
        type="{{ $type }}"
        @if ($invalid) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except(['class', 'id'])->class(['field', 'field-invalid' => $invalid]) }}
    >

    @if ($hint && ! $invalid)
        <p class="text-xs text-grey-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p id="{{ $id }}-error" class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
