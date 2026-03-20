<button {{ $attributes->merge(['type' => 'submit', 'class' => 'accent-button inline-flex items-center justify-center rounded-full border border-transparent px-4 py-2 text-sm font-semibold tracking-[0.14em] uppercase transition']) }}>
    {{ $slot }}
</button>
