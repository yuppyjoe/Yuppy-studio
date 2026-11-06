@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-md bg-success-soft px-4 py-3 text-sm text-success']) }}>
        {{ $status }}
    </div>
@endif
