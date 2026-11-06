@props([
    'label' => null,
    'name',
    'options' => [],
    'placeholder' => null,
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

    <select
        id="{{ $id }}"
        @if ($invalid) aria-invalid="true" @endif
        {{ $attributes->except(['class', 'id'])->class(['field pr-10', 'field-invalid' => $invalid]) }}
    >
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $value => $text)
            <option value="{{ $value }}">{{ $text }}</option>
        @endforeach
        {{ $slot }}
    </select>

    @if ($hint && ! $invalid)
        <p class="text-xs text-grey-500">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
