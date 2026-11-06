<?php

use App\Models\Customer;
use App\Models\PhotobookProject;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Overview')] class extends Component
{
    public function with(): array
    {
        $user = auth()->user();
        $profile = $user->photographerProfile;
        $branding = $profile?->brandingProfile;

        $checklist = [
            ['label' => __('Add your studio phone and email'), 'done' => filled($profile?->phone) && filled($profile?->email)],
            ['label' => __('Set up your book branding'), 'done' => $branding !== null],
            ['label' => __('Upload your studio logo'), 'done' => (bool) $branding?->hasLogo()],
            ['label' => __('Add your first client'), 'done' => Customer::visibleTo($user)->exists()],
        ];

        return [
            'profile' => $profile,
            'clientCount' => Customer::visibleTo($user)->count(),
            'projectCount' => PhotobookProject::visibleTo($user)->count(),
            'recentProjects' => PhotobookProject::visibleTo($user)->with('customer')->latest()->limit(5)->get(),
            'checklist' => $checklist,
            'completed' => collect($checklist)->where('done', true)->count(),
        ];
    }
}; ?>

<div>
    <x-ui.page-header :eyebrow="$profile?->studio_name" :title="__('Good to see you, :name', ['name' => Str::before(auth()->user()->name, ' ')])">
        <x-slot name="actions">
            <x-ui.button variant="secondary" :href="route('studio.profile')" wire:navigate>{{ __('Edit studio') }}</x-ui.button>
            <x-ui.button :href="route('studio.customers.create')" wire:navigate><x-ui.icon name="plus" class="h-4 w-4" /> {{ __('Add client') }}</x-ui.button>
        </x-slot>
    </x-ui.page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="grid gap-6 sm:grid-cols-2 lg:col-span-2">
            <x-ui.stat :label="__('Clients')" :value="$clientCount" :href="route('studio.customers.index')" :hint="__('People you design books for')" />
            <x-ui.stat :label="__('Projects')" :value="$projectCount" :href="route('studio.projects.index')" :hint="__('Photobooks in your studio')" />

            <x-ui.card :title="__('Recent projects')" class="sm:col-span-2" :padded="false">
                @forelse ($recentProjects as $project)
                    <a href="{{ route('studio.projects.show', $project) }}" wire:navigate class="flex items-center justify-between gap-4 border-b border-grey-100 px-6 py-4 transition last:border-0 hover:bg-ivory-50 sm:px-8">
                        <span class="min-w-0">
                            <span class="block truncate font-medium text-ink">{{ $project->title }}</span>
                            <span class="block text-xs text-grey-500">{{ $project->customer?->name ?? __('No client yet') }}</span>
                        </span>
                        <x-ui.badge>{{ $project->status->label() }}</x-ui.badge>
                    </a>
                @empty
                    <x-ui.empty-state icon="book" :title="__('No projects yet')"
                        :description="__('Photobook design opens in the next release. Your projects will appear here.')" />
                @endforelse
            </x-ui.card>
        </div>

        <x-ui.card :title="__('Studio setup')" :description="__(':done of :total complete', ['done' => $completed, 'total' => count($checklist)])">
            <div class="mb-6 h-1 overflow-hidden rounded-full bg-grey-100">
                <div class="h-full bg-champagne-500" style="width: {{ (int) round($completed / count($checklist) * 100) }}%"></div>
            </div>
            <ul class="space-y-4">
                @foreach ($checklist as $item)
                    <li class="flex items-start gap-3 text-sm">
                        <span @class([
                            'mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border',
                            'border-champagne-500 bg-champagne-500 text-white' => $item['done'],
                            'border-grey-300' => ! $item['done'],
                        ])>
                            @if ($item['done']) <x-ui.icon name="check" class="h-3 w-3" /> @endif
                        </span>
                        <span @class(['text-grey-400 line-through' => $item['done'], 'text-charcoal' => ! $item['done']])>{{ $item['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </x-ui.card>
    </div>
</div>
