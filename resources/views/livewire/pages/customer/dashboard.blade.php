<?php

use App\Models\PhotobookProject;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('My photobooks')] class extends Component
{
    public function with(): array
    {
        return [
            'projects' => PhotobookProject::visibleTo(auth()->user())
                ->with(['occasion', 'bookFormat'])
                ->latest()
                ->get(),
        ];
    }
}; ?>

<div>
    <x-ui.page-header :eyebrow="__('Welcome, :name', ['name' => Str::before(auth()->user()->name, ' ')])" :title="__('My photobooks')"
        :description="__('The books being made for you, all in one place.')" />

    @if ($projects->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="book" :title="__('Your shelf is empty, for now')"
                :description="__('When a photographer shares a photobook with you, or when you start your own in the next release, it will appear here.')" />
        </x-ui.card>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($projects as $project)
                <a href="{{ route('customer.projects.show', $project) }}" wire:navigate wire:key="project-{{ $project->id }}"
                   class="focus-ring group rounded-lg border border-grey-200/80 bg-white p-6 shadow-soft transition hover:border-champagne-300">
                    <div class="mb-6 flex aspect-[4/3] items-center justify-center rounded-md bg-ivory-200">
                        <x-ui.icon name="book" class="h-8 w-8 text-champagne-500" />
                    </div>
                    <p class="eyebrow">{{ $project->occasion?->name ?? __('Photobook') }}</p>
                    <h2 class="mt-1 text-2xl">{{ $project->title }}</h2>
                    <div class="mt-4 flex items-center justify-between text-xs text-grey-500">
                        <span>{{ $project->bookFormat?->name }}</span>
                        <x-ui.badge>{{ $project->status->label() }}</x-ui.badge>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
