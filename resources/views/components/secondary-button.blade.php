<button {{ $attributes->merge(['type' => 'button', 'class' => 'page-outline-button justify-center disabled:opacity-25']) }}>
    {{ $slot }}
</button>
