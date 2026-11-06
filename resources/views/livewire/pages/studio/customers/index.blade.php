<?php

use App\Models\Customer;
use App\Services\AuditLogger;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Clients')] class extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public ?int $deletingId = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Customer::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $customer = Customer::visibleTo(auth()->user())->findOrFail($id);
        $this->authorize('delete', $customer);

        $this->deletingId = $customer->id;
        $this->dispatch('open-modal', 'delete-client');
    }

    public function delete(AuditLogger $audit): void
    {
        $customer = Customer::visibleTo(auth()->user())->findOrFail($this->deletingId);
        $this->authorize('delete', $customer);

        $audit->log('customer.deleted', $customer, old: $customer->only(['name', 'email', 'phone']));
        $customer->delete();

        $this->deletingId = null;
        $this->dispatch('close-modal', 'delete-client');
        $this->dispatch('toast', message: __('Client removed.'));
    }

    public function with(): array
    {
        return [
            'customers' => Customer::visibleTo(auth()->user())
                ->withCount('projects')
                ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")))
                ->orderBy('name')
                ->paginate(15),
        ];
    }
}; ?>

<div>
    <x-ui.page-header :eyebrow="__('Your studio')" :title="__('Clients')" :description="__('The people you design photobooks for. Only you can see this list.')">
        <x-slot name="actions">
            <x-ui.button :href="route('studio.customers.create')" wire:navigate><x-ui.icon name="plus" class="h-4 w-4" /> {{ __('Add client') }}</x-ui.button>
        </x-slot>
    </x-ui.page-header>

    <x-ui.card :padded="false">
        <div class="border-b border-grey-100 px-6 py-4 sm:px-8">
            <label class="relative block max-w-sm">
                <span class="sr-only">{{ __('Search clients') }}</span>
                <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-grey-400" />
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or email') }}" class="field pl-9">
            </label>
        </div>

        @if ($customers->isEmpty())
            <x-ui.empty-state icon="users"
                :title="$search === '' ? __('No clients yet') : __('No matching clients')"
                :description="$search === '' ? __('Add a client to keep their details ready for their photobook.') : __('Try a different name or email.')">
                @if ($search === '')
                    <x-ui.button :href="route('studio.customers.create')" wire:navigate>{{ __('Add your first client') }}</x-ui.button>
                @endif
            </x-ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-grey-100">
                    <thead>
                        <tr>
                            <th class="table-head sm:pl-8">{{ __('Name') }}</th>
                            <th class="table-head hidden md:table-cell">{{ __('Contact') }}</th>
                            <th class="table-head">{{ __('Projects') }}</th>
                            <th class="table-head sm:pr-8"><span class="sr-only">{{ __('Actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        @foreach ($customers as $customer)
                            <tr wire:key="customer-{{ $customer->id }}" class="transition hover:bg-ivory-50">
                                <td class="table-cell sm:pl-8">
                                    <span class="font-medium text-ink">{{ $customer->name }}</span>
                                    <span class="block text-xs text-grey-500 md:hidden">{{ $customer->email }}</span>
                                </td>
                                <td class="table-cell hidden md:table-cell">
                                    <span class="block">{{ $customer->email ?? '—' }}</span>
                                    <span class="block text-xs text-grey-500">{{ $customer->phone }}</span>
                                </td>
                                <td class="table-cell">{{ $customer->projects_count }}</td>
                                <td class="table-cell text-right sm:pr-8">
                                    <div class="flex justify-end gap-1">
                                        <x-ui.button size="sm" variant="ghost" :href="route('studio.customers.edit', $customer)" wire:navigate>{{ __('Edit') }}</x-ui.button>
                                        <x-ui.button size="sm" variant="danger-ghost" type="button" wire:click="confirmDelete({{ $customer->id }})">{{ __('Remove') }}</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($customers->hasPages())
                <div class="border-t border-grey-100 px-6 py-4 sm:px-8">{{ $customers->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <x-ui.modal name="delete-client" :title="__('Remove this client?')">
        {{ __('Their details will be deleted. Any projects linked to them stay in your studio without a client.') }}
        <x-slot name="footer">
            <x-ui.button variant="secondary" type="button" x-on:click="$dispatch('close')">{{ __('Cancel') }}</x-ui.button>
            <x-ui.button variant="danger" type="button" wire:click="delete" wire:loading.attr="disabled">{{ __('Remove client') }}</x-ui.button>
        </x-slot>
    </x-ui.modal>
</div>
