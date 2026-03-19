<button {{ $attributes->merge(['type' => 'button', 'class' => 'page-outline-button justify-center disabled:opacity-25 focus:outline-none focus:ring-2 focus:ring-amber-400/30 focus:ring-offset-2']) }}>
    {{ $slot }}
</button>
