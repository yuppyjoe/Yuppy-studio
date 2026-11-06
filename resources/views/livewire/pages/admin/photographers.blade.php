<?php

use App\Models\PhotographerProfile;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Photographers')] class extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', PhotographerProfile::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'photographers' => PhotographerProfile::visibleTo(auth()->user())
                ->with(['user', 'brandingProfile'])
                ->withCount(['customers', 'projects'])
                ->when($this->search !== '', fn ($q) => $q->where(fn ($q) => $q
                    ->where('studio_name', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%")->orWhere('email', 'like', "%{$this->search}%"))))
                ->orderBy('studio_name')
                ->paginate(20),
        ];
    }
}; ?>

<div>
    <x-ui.page-header :eyebrow="__('Yuppy administration')" :title="__('Photographers')" :description="__('Read-only for now. Account management arrives in a later release.')" />

    <x-ui.card :padded="false">
        <div class="border-b border-grey-100 px-6 py-4 sm:px-8">
            <label class="relative block max-w-sm">
                <span class="sr-only">{{ __('Search photographers') }}</span>
                <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-grey-400" />
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search studio, name or email') }}" class="field pl-9">
            </label>
        </div>

        @if ($photographers->isEmpty())
            <x-ui.empty-state icon="camera" :title="__('No photographers found')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-grey-100">
                    <thead>
                        <tr>
                            <th class="table-head sm:pl-8">{{ __('Studio') }}</th>
                            <th class="table-head hidden md:table-cell">{{ __('Account') }}</th>
                            <th class="table-head hidden lg:table-cell">{{ __('City') }}</th>
                            <th class="table-head">{{ __('Clients') }}</th>
                            <th class="table-head hidden sm:table-cell">{{ __('Projects') }}</th>
                            <th class="table-head sm:pr-8">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        @foreach ($photographers as $profile)
                            <tr wire:key="ph-{{ $profile->id }}">
                                <td class="table-cell sm:pl-8">
                                    <div class="flex items-center gap-3">
                                        @if ($profile->brandingProfile?->hasLogo())
                                            <img src="{{ $profile->brandingProfile->logoUrl() }}" alt="" class="h-9 w-9 rounded border border-grey-200 bg-white object-contain p-0.5">
                                        @else
                                            <span class="flex h-9 w-9 items-center justify-center rounded bg-ivory-200 font-serif text-ink">{{ mb_substr($profile->studio_name, 0, 1) }}</span>
                                        @endif
                                        <span>
                                            <span class="block font-medium text-ink">{{ $profile->studio_name }}</span>
                                            <span class="block text-xs text-grey-500">{{ __('Joined :date', ['date' => $profile->created_at?->format('M Y')]) }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="table-cell hidden md:table-cell">
                                    <span class="block">{{ $profile->user->name }}</span>
                                    <span class="block text-xs text-grey-500">{{ $profile->user->email }}</span>
                                </td>
                                <td class="table-cell hidden lg:table-cell">{{ $profile->city ?? '—' }}</td>
                                <td class="table-cell">{{ $profile->customers_count }}</td>
                                <td class="table-cell hidden sm:table-cell">{{ $profile->projects_count }}</td>
                                <td class="table-cell sm:pr-8">
                                    <x-ui.badge :tone="$profile->user->isActive() ? 'success' : 'danger'">{{ $profile->user->status->label() }}</x-ui.badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($photographers->hasPages())
                <div class="border-t border-grey-100 px-6 py-4 sm:px-8">{{ $photographers->links() }}</div>
            @endif
        @endif
    </x-ui.card>
</div>
