{{--
    Global toast stack. Trigger from Livewire: $this->dispatch('toast', message: 'Saved', type: 'success');
    or across a redirect: session()->flash('toast', ['message' => 'Saved', 'type' => 'success']).
--}}
@php($flash = session('toast'))

<div
    x-data="{
        toasts: [],
        add(detail) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message: detail.message, type: detail.type ?? 'success' });
            setTimeout(() => this.remove(id), 4500);
        },
        remove(id) { this.toasts = this.toasts.filter(t => t.id !== id) },
    }"
    x-init="@if ($flash) add(@js($flash)) @endif"
    x-on:toast.window="add($event.detail)"
    class="pointer-events-none fixed inset-x-4 bottom-4 z-[60] flex flex-col items-end gap-2 sm:inset-x-auto sm:right-6 sm:bottom-6"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-2 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto flex w-full items-start gap-3 rounded-md bg-ink px-4 py-3 text-sm text-ivory-100 shadow-lift sm:w-80"
            role="status"
        >
            <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full"
                  :class="toast.type === 'error' ? 'bg-red-400' : 'bg-champagne-400'"></span>
            <p class="flex-1" x-text="toast.message"></p>
            <button type="button" class="text-grey-400 transition hover:text-ivory-50" x-on:click="remove(toast.id)" aria-label="{{ __('Dismiss') }}">
                <x-ui.icon name="x" class="h-4 w-4" />
            </button>
        </div>
    </template>
</div>
