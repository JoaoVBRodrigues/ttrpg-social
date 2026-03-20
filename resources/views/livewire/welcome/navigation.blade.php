<nav class="-mx-3 flex flex-1 justify-end">
    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="theme-link rounded-md px-3 py-2 transition"
        >
            Dashboard
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="theme-link rounded-md px-3 py-2 transition"
        >
            Log in
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="theme-link rounded-md px-3 py-2 transition"
            >
                Register
            </a>
        @endif
    @endauth
</nav>
