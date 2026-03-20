@php($currentLocale = app()->getLocale())

<div class="control-pill p-1">
    @foreach (['en' => 'EN', 'pt_BR' => 'PT'] as $locale => $label)
        <a
            href="{{ route('locale.update', $locale) }}"
            class="{{ $currentLocale === $locale ? 'control-pill-active' : 'theme-link' }} rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] transition"
        >
            {{ $label }}
        </a>
    @endforeach
</div>
