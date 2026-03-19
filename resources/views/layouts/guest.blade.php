<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TTRPG Social') }}</title>

        <!-- Fonts -->
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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="app-shell flex min-h-screen flex-col px-4 py-6 sm:px-6">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-4 theme-link" wire:navigate>
                    <x-application-logo class="h-12 w-12 fill-current text-[color:var(--app-accent)]" />
                    <div>
                        <p class="font-display text-lg font-semibold">{{ config('app.name', 'TTRPG Social') }}</p>
                        <p class="text-sm text-[color:var(--app-text-muted)]">{{ __('Find your next table') }}</p>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    <x-locale-switcher />
                    <x-theme-toggle />
                </div>
            </div>

            <div class="mx-auto flex w-full max-w-6xl flex-1 items-center justify-center py-10">
                <div class="grid w-full items-center gap-8 lg:grid-cols-[1.1fr,0.9fr]">
                    <div class="hidden rounded-[2rem] border border-white/10 bg-slate-950/70 p-10 text-white shadow-2xl lg:block">
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-amber-300">{{ __('Tabletop community') }}</p>
                        <h1 class="mt-5 font-display text-4xl font-semibold leading-tight">{{ __('Gather your party, manage your campaign, and keep every session moving.') }}</h1>
                        <p class="mt-6 max-w-xl text-base leading-7 text-slate-300">{{ __('Coordinate your table with public campaign discovery, RSVP tracking, realtime chat, and dice rolls built for long-running TTRPG groups.') }}</p>
                        <div class="mt-8 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-amber-300">{{ __('Realtime') }}</p>
                                <p class="mt-2 text-sm text-slate-200">{{ __('Live campaign chat and fast table updates.') }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-amber-300">{{ __('Campaigns') }}</p>
                                <p class="mt-2 text-sm text-slate-200">{{ __('Public listings, invites, and join requests.') }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-xs uppercase tracking-[0.2em] text-amber-300">{{ __('Compendium') }}</p>
                                <p class="mt-2 text-sm text-slate-200">{{ __('Keep primers, links, and table notes together.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="surface-panel mx-auto w-full max-w-md overflow-hidden rounded-[2rem] px-6 py-6 sm:px-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
