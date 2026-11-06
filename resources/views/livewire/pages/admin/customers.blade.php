<?php

use App\Models\Customer;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Customers')] class extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Customer::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'customers' => Customer::visibleTo(auth()->user())
                ->with(['user', 'photographerProfile'])
                ->withCount('projects')
                ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")))
                ->orderBy('name')
                ->paginate(20),
        ];
    }
}; ?>

<div>
    <x-ui.page-header :eyebrow="__('Yuppy administration')" :title="__('Customers')"
        :description="__('Self-registered customers and photographers\' clients. Read-only for now.')" />

    <x-ui.card :padded="false">
        <div class="border-b border-grey-100 px-6 py-4 sm:px-8">
            <label class="relative block max-w-sm">
                <span class="sr-only">{{ __('Search customers') }}</span>
                <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-grey-400" />
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search by name or email') }}" class="field pl-9">
            </label>
        </div>

        @if ($customers->isEmpty())
            <x-ui.empty-state icon="users" :title="__('No customers found')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-grey-100">
                    <thead>
                        <tr>
                            <th class="table-head sm:pl-8">{{ __('Name') }}</th>
                            <th class="table-head hidden md:table-cell">{{ __('Contact') }}</th>
                            <th class="table-head">{{ __('Source') }}</th>
                            <th class="table-head sm:pr-8">{{ __('Projects') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        @foreach ($customers as $customer)
                            <tr wire:key="c-{{ $customer->id }}">
                                <td class="table-cell sm:pl-8"><span class="font-medium text-ink">{{ $customer->name }}</span></td>
                                <td class="table-cell hidden md:table-cell">
                                    <span class="block">{{ $customer->email ?? '—' }}</span>
                                    <span class="block text-xs text-grey-500">{{ $customer->phone }}</span>
                                </td>
                                <td class="table-cell">
                                    @if ($customer->photographerProfile)
                                        <x-ui.badge>{{ $customer->photographerProfile->studio_name }}</x-ui.badge>
                                    @else
                                        <x-ui.badge tone="gold">{{ __('Yuppy direct') }}</x-ui.badge>
                                    @endif
                                </td>
                                <td class="table-cell sm:pr-8">{{ $customer->projects_count }}</td>
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
</div>
