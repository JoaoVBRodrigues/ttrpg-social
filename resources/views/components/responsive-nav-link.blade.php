@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-2xl border-l-4 ps-3 pe-4 py-3 text-start text-base font-semibold transition duration-150 ease-in-out'
            : 'block w-full rounded-2xl border-l-4 border-transparent ps-3 pe-4 py-3 text-start text-base font-medium transition duration-150 ease-in-out';
@endphp

<a
    {{ $attributes->merge(['class' => $classes]) }}
    style="{{ ($active ?? false)
        ? 'border-color: color-mix(in srgb, var(--app-accent) 72%, transparent); background: color-mix(in srgb, var(--app-accent) 10%, transparent); color: var(--app-text);'
        : 'color: var(--app-text-muted);' }}"
>
    {{ $slot }}
</a>
