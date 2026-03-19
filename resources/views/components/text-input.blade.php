@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'form-surface rounded-2xl border px-4 py-3 shadow-sm focus:border-amber-400/40 focus:ring-amber-400/30']) }}>
