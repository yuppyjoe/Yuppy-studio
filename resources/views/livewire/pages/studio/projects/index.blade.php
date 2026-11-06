<?php

use App\Models\PhotobookProject;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Title('Projects')] class extends Component
{
    use WithPagination;

    public function mount(): void
    {
        $this->authorize('viewAny', PhotobookProject::class);
    }

    public function with(): array
    {
        return [
            'projects' => PhotobookProject::visibleTo(auth()->user())
                ->with(['customer', 'occasion', 'bookFormat'])
                ->latest()
                ->paginate(15),
        ];
    }
}; ?>

<div>
    <x-ui.page-header :eyebrow="__('Your studio')" :title="__('Projects')" :description="__('Every photobook in your studio, branded with your studio name.')" />

    <x-ui.card :padded="false">
        @if ($projects->isEmpty())
            <x-ui.empty-state icon="book" :title="__('No projects yet')"
                :description="__('Photobook design opens in the next release. Your projects will appear here.')" />
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-grey-100">
                    <thead>
                        <tr>
                            <th class="table-head sm:pl-8">{{ __('Title') }}</th>
                            <th class="table-head hidden md:table-cell">{{ __('Client') }}</th>
                            <th class="table-head hidden md:table-cell">{{ __('Occasion') }}</th>
                            <th class="table-head sm:pr-8">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-grey-100">
                        @foreach ($projects as $project)
                            <tr wire:key="project-{{ $project->id }}" class="transition hover:bg-ivory-50">
                                <td class="table-cell sm:pl-8">
                                    <a href="{{ route('studio.projects.show', $project) }}" wire:navigate class="link">{{ $project->title }}</a>
                                </td>
                                <td class="table-cell hidden md:table-cell">{{ $project->customer?->name ?? '—' }}</td>
                                <td class="table-cell hidden md:table-cell">{{ $project->occasion?->name ?? '—' }}</td>
                                <td class="table-cell sm:pr-8"><x-ui.badge>{{ $project->status->label() }}</x-ui.badge></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($projects->hasPages())
                <div class="border-t border-grey-100 px-6 py-4 sm:px-8">{{ $projects->links() }}</div>
            @endif
        @endif
    </x-ui.card>
</div>
