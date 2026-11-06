<?php

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\ManufacturerTemplate;
use App\Models\PhotobookProject;
use App\Models\PhotographerProfile;
use Livewire\Attributes\Title;
use Livewire\Volt\Component;

new #[Title('Overview')] class extends Component
{
    public function with(): array
    {
        $user = auth()->user();

        return [
            'photographers' => PhotographerProfile::visibleTo($user)->count(),
            'customers' => Customer::visibleTo($user)->count(),
            'projects' => PhotobookProject::visibleTo($user)->count(),
            'verifiedTemplates' => ManufacturerTemplate::verified()->count(),
            'activity' => AuditLog::with('user')->latest('created_at')->latest('id')->limit(12)->get(),
        ];
    }
}; ?>

<div>
    <x-ui.page-header :eyebrow="__('Yuppy administration')" :title="__('Overview')">
        <x-slot name="actions">
            <x-ui.button variant="secondary" :href="route('admin.branding')" wire:navigate>{{ __('Yuppy branding') }}</x-ui.button>
        </x-slot>
    </x-ui.page-header>

    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat :label="__('Photographers')" :value="$photographers" :href="route('admin.photographers')" />
        <x-ui.stat :label="__('Customers')" :value="$customers" :href="route('admin.customers')" />
        <x-ui.stat :label="__('Projects')" :value="$projects" />
        <x-ui.stat :label="__('Verified templates')" :value="$verifiedTemplates" :hint="__('Manufacturer measurements awaiting verified spec sheets')" />
    </div>

    <x-ui.card :title="__('Recent activity')" :description="__('Sign-ins, profile and branding changes across the platform.')" class="mt-8" :padded="false">
        @forelse ($activity as $entry)
            <div class="flex items-start gap-4 border-b border-grey-100 px-6 py-4 last:border-0 sm:px-8">
                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-ivory-200">
                    <x-ui.icon name="activity" class="h-4 w-4 text-grey-500" />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-ink">
                        <span class="font-medium">{{ $entry->user?->name ?? __('System') }}</span>
                        <span class="text-grey-600">{{ Str::of($entry->event)->replace(['_', '.'], ' ') }}</span>
                    </p>
                    <p class="text-xs text-grey-500">{{ $entry->created_at?->diffForHumans() }} · {{ $entry->ip_address }}</p>
                </div>
            </div>
        @empty
            <x-ui.empty-state icon="activity" :title="__('No activity yet')" :description="__('Sign-ins and changes will be recorded here.')" />
        @endforelse
    </x-ui.card>
</div>
