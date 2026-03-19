@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold uppercase tracking-[0.18em]']) }} style="color: var(--app-text-muted);">
    {{ $value ?? $slot }}
</label>
