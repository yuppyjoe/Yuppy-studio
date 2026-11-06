<?php

use App\Models\PhotobookProject;
use App\Services\BrandingResolver;
use Livewire\Volt\Component;

new class extends Component
{
    public PhotobookProject $project;

    public function mount(PhotobookProject $project): void
    {
        $this->authorize('view', $project);
        $this->project = $project;
    }

    public function rendering($view): void
    {
        $view->title($this->project->title);
    }

    public function with(BrandingResolver $resolver): array
    {
        $this->project->loadMissing(['customer', 'occasion', 'bookFormat', 'photographerProfile.brandingProfile']);

        return ['branding' => $resolver->resolve($this->project)];
    }
}; ?>

<div>
    <a href="{{ route('customer.dashboard') }}" wire:navigate class="focus-ring mb-6 inline-flex items-center gap-2 rounded text-sm text-grey-500 transition hover:text-ink">
        <x-ui.icon name="arrow-left" class="h-4 w-4" /> {{ __('My photobooks') }}
    </a>
    <x-ui.page-header :eyebrow="__('Photobook')" :title="$project->title" />
    <x-project-summary :project="$project" :branding="$branding" />
</div>
