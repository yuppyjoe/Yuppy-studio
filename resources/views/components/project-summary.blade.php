{{-- Read-only project details. Props: project (PhotobookProject), branding (BrandingProfile|null, from BrandingResolver) --}}
@props(['project', 'branding'])

<div class="grid gap-6 lg:grid-cols-3">
    <x-ui.card :title="__('Details')" class="lg:col-span-2">
        <dl class="grid gap-x-8 gap-y-6 sm:grid-cols-2">
            @foreach ([
                __('Status') => $project->status->label(),
                __('Occasion') => $project->occasion?->name ?? __('Not chosen yet'),
                __('Book format') => $project->bookFormat?->name ?? __('Not chosen yet'),
                __('Client') => $project->customer?->name ?? __('No client linked'),
                __('Created') => $project->created_at?->format('j M Y'),
                __('Last updated') => $project->updated_at?->diffForHumans(),
            ] as $term => $value)
                <div>
                    <dt class="eyebrow">{{ $term }}</dt>
                    <dd class="mt-1.5 text-ink">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </x-ui.card>

    <x-ui.card :title="__('Branding')">
        @if ($branding)
            <div class="flex items-center gap-4">
                @if ($branding->hasLogo())
                    <img src="{{ $branding->logoUrl() }}" alt="{{ $branding->brand_name }}" class="h-14 w-14 rounded-md border border-grey-200 bg-white object-contain p-1.5">
                @endif
                <div class="min-w-0">
                    <p class="font-serif text-xl text-ink">{{ $branding->brand_name }}</p>
                    @if ($branding->tagline)
                        <p class="text-xs text-grey-500">{{ $branding->tagline }}</p>
                    @endif
                </div>
            </div>
        @else
            <p class="text-sm text-grey-500">{{ __('This book is unbranded.') }}</p>
        @endif
        <p class="mt-5 text-xs text-grey-400">{{ __('Mode: :mode', ['mode' => $project->branding_mode->label()]) }}</p>
    </x-ui.card>
</div>

<x-ui.card class="mt-6">
    <x-ui.empty-state icon="sparkle" :title="__('Design opens soon')"
        :description="__('Photo uploads and the book designer arrive in the next release. This project is saved and ready.')" class="!py-8" />
</x-ui.card>
