<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full border border-rose-400/30 bg-rose-500/90 px-4 py-2 text-sm font-semibold uppercase tracking-[0.14em] text-white transition hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-400/40 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
