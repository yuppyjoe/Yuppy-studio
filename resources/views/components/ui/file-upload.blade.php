{{--
    Livewire file upload with progress and preview.
    Usage: <x-ui.file-upload name="logo" wire:model="logo" accept="image/png,image/jpeg,image/webp"
                             :preview="$logo?->temporaryUrl() ?? $currentUrl" />
--}}
@props([
    'label' => null,
    'name',
    'hint' => null,
    'preview' => null,
    'accept' => null,
])

@php
    $id = $attributes->get('id', 'upload-'.str_replace('.', '-', $name));
    $invalid = $errors->has($name);
@endphp

<div
    x-data="{ uploading: false, progress: 0 }"
    x-on:livewire-upload-start="uploading = true; progress = 0"
    x-on:livewire-upload-finish="uploading = false"
    x-on:livewire-upload-cancel="uploading = false"
    x-on:livewire-upload-error="uploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
    {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}
>
    @if ($label)
        <span class="block text-sm font-medium text-charcoal">{{ $label }}</span>
    @endif

    <div @class([
        'flex flex-col gap-5 rounded-lg border border-dashed p-5 sm:flex-row sm:items-center',
        'border-grey-300 bg-ivory-50' => ! $invalid,
        'border-danger bg-danger-soft/40' => $invalid,
    ])>
        <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-md border border-grey-200 bg-white">
            @if ($preview)
                <img src="{{ $preview }}" alt="" class="h-full w-full object-contain p-2">
            @else
                <x-ui.icon name="photo" class="h-7 w-7 text-grey-300" />
            @endif
        </div>

        <div class="min-w-0 flex-1 space-y-2">
            <label for="{{ $id }}" class="focus-ring inline-flex cursor-pointer items-center gap-2 rounded-md border border-grey-200 bg-white px-3.5 py-2 text-sm font-medium text-ink transition hover:border-grey-300 hover:bg-ivory-100">
                <x-ui.icon name="upload" class="h-4 w-4" />
                <span>{{ $preview ? __('Replace file') : __('Choose file') }}</span>
            </label>
            <input id="{{ $id }}" type="file" class="sr-only" @if ($accept) accept="{{ $accept }}" @endif {{ $attributes->except(['class', 'id']) }}>

            @if ($hint)
                <p class="text-xs text-grey-500">{{ $hint }}</p>
            @endif

            <div x-show="uploading" x-cloak class="h-1 w-full overflow-hidden rounded-full bg-grey-100">
                <div class="h-full bg-champagne-500 transition-all" :style="`width: ${progress}%`"></div>
            </div>

            {{ $slot }}
        </div>
    </div>

    @error($name)
        <p class="text-xs text-danger">{{ $message }}</p>
    @enderror
</div>
