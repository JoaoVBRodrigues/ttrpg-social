<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $profileUser->name }} · {{ config('app.name', 'TTRPG Social') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=cinzel:400,500,600,700|outfit:300,400,500,600,700&display=swap" rel="stylesheet" />

        <script>
            (() => {
                const storedTheme = localStorage.getItem('ttrpg-theme');
                const theme = storedTheme ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.dataset.theme = theme;
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        @php($profile = $profileResource->resolve(request()))

        <div class="app-shell">
            <div class="mx-auto flex min-h-screen max-w-5xl flex-col px-4 py-10 sm:px-6 lg:px-8">
                <header class="flex items-center justify-between gap-4">
                    <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="page-link text-sm font-medium transition">
                        {{ __('Back') }}
                    </a>

                    @auth
                        @if (auth()->id() === $profileUser->id)
                            <a href="{{ route('profile') }}" class="page-outline-button">
                                {{ __('Edit profile') }}
                            </a>
                        @endif
                    @endauth
                </header>

                <main class="mt-8 flex-1 space-y-6">
                    <section class="page-card">
                        <p class="eyebrow">{{ __('TTRPG profile') }}</p>
                        <div class="mt-4 grid gap-8 md:grid-cols-[1.5fr,1fr]">
                            <div>
                                <h1 class="font-display text-4xl">{{ $profile['name'] }}</h1>
                                <p class="mt-2 text-sm" style="color: var(--app-text-muted);">{{ '@'.$profile['username'] }}</p>
                                <p class="mt-6 whitespace-pre-line text-sm leading-7" style="color: var(--app-text-muted);">
                                    {{ $profile['bio'] ?: __('This user has not added a bio yet.') }}
                                </p>
                            </div>

                            <div class="page-card-soft !p-6">
                                <h2 class="font-display text-2xl">{{ __('Preferences') }}</h2>
                                <dl class="mt-4 space-y-4 text-sm" style="color: var(--app-text-muted);">
                                    <div>
                                        <dt class="font-medium" style="color: var(--app-text);">{{ __('Preferred role') }}</dt>
                                        <dd class="mt-1">{{ ucfirst(str_replace('_', ' ', $profile['preferred_role'])) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-medium" style="color: var(--app-text);">{{ __('Timezone') }}</dt>
                                        <dd class="mt-1">{{ $profile['timezone'] }}</dd>
                                    </div>
                                    @if ($profile['email'])
                                        <div>
                                            <dt class="font-medium" style="color: var(--app-text);">{{ __('Email') }}</dt>
                                            <dd class="mt-1">{{ $profile['email'] }}</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </section>

                    <section class="grid gap-6 md:grid-cols-2">
                        <div class="page-card">
                            <h2 class="font-display text-2xl">{{ __('Favorite systems') }}</h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse ($profile['favorite_systems'] as $system)
                                    <span class="page-chip !normal-case !tracking-[0.08em]">{{ $system }}</span>
                                @empty
                                    <p class="text-sm" style="color: var(--app-text-muted);">{{ __('No favorite systems listed yet.') }}</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="page-card">
                            <h2 class="font-display text-2xl">{{ __('Availability') }}</h2>
                            <div class="mt-4 space-y-3">
                                @forelse ($profile['availability'] as $slot)
                                    <div class="page-card-soft !p-4 text-sm" style="color: var(--app-text-muted);">
                                        <span class="font-medium" style="color: var(--app-text);">{{ $slot['day'] ?? __('Day') }}</span>
                                        <span class="ml-2">{{ $slot['window'] ?? '' }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm" style="color: var(--app-text-muted);">{{ __('No availability shared yet.') }}</p>
                                @endforelse
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </body>
</html>
