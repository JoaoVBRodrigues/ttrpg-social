<button {{ $attributes->merge(['type' => 'submit', 'class' => 'accent-button inline-flex items-center justify-center rounded-full border border-transparent px-4 py-2 text-sm font-semibold tracking-[0.14em] uppercase transition focus:outline-none focus:ring-2 focus:ring-amber-400/40 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
