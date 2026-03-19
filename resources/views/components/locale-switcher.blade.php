@php($currentLocale = app()->getLocale())

<div class="inline-flex items-center rounded-full border border-white/15 bg-white/10 p-1 dark:border-white/10 dark:bg-slate-900/60">
    @foreach (['en' => 'EN', 'pt_BR' => 'PT'] as $locale => $label)
        <a
            href="{{ route('locale.update', $locale) }}"
            class="{{ $currentLocale === $locale ? 'bg-amber-400 text-slate-950 shadow-sm' : 'text-slate-600 hover:text-slate-950 dark:text-slate-300 dark:hover:text-white' }} rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] transition"
        >
            {{ $label }}
        </a>
    @endforeach
</div>
