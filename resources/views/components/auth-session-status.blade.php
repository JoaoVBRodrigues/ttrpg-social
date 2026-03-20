@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'callout callout-success font-medium']) }}>
        {{ $status }}
    </div>
@endif
