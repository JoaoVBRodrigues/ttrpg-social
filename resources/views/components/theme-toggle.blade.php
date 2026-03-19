<button
    type="button"
    data-theme-toggle
    class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700 transition hover:border-amber-400/50 hover:text-slate-950 dark:border-white/10 dark:bg-slate-900/60 dark:text-slate-200 dark:hover:border-amber-300/40 dark:hover:text-white"
    aria-label="{{ __('Toggle theme') }}"
>
    <svg data-theme-icon class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path d="M10 1.5a8.5 8.5 0 1 0 0 17V1.5Z" />
        <path d="M10 1.5a8.5 8.5 0 0 1 0 17V1.5Z" class="opacity-25" />
    </svg>
    <span data-theme-label>{{ __('Theme') }}</span>
</button>
