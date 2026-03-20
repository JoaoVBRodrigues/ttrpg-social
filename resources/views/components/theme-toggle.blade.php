<button
    type="button"
    data-theme-toggle
    class="control-pill"
    aria-label="{{ __('Toggle theme') }}"
>
    <svg data-theme-icon class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path d="M10 1.5a8.5 8.5 0 1 0 0 17V1.5Z" />
        <path d="M10 1.5a8.5 8.5 0 0 1 0 17V1.5Z" class="opacity-25" />
    </svg>
    <span data-theme-label>{{ __('Theme') }}</span>
</button>
