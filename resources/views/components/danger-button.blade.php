<button {{ $attributes->merge(['type' => 'submit', 'class' => 'danger-button inline-flex items-center justify-center rounded-full border px-4 py-2 text-sm font-semibold uppercase tracking-[0.14em] transition focus:outline-none focus:ring-2 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
