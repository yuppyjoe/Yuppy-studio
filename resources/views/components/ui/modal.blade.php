{{--
    Styled wrapper around the base <x-modal>. Open with:
    $dispatch('open-modal', 'name') (Alpine) or $this->dispatch('open-modal', 'name') (Livewire).
--}}
@props([
    'name',
    'title',
    'maxWidth' => 'md',
])

<x-modal :name="$name" :maxWidth="$maxWidth" focusable>
    <div class="px-6 pb-2 pt-6 sm:px-8 sm:pt-8">
        <h2 class="text-2xl">{{ $title }}</h2>
        <div class="mt-3 text-sm text-grey-600">{{ $slot }}</div>
    </div>
    @isset($footer)
        <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-grey-100 bg-ivory-50 px-6 py-4 sm:px-8">
            {{ $footer }}
        </div>
    @endisset
</x-modal>
