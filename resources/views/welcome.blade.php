<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'TTRPG Social') }}</title>

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
        <div class="app-shell overflow-x-hidden" style="color: var(--app-text);">
            <header class="relative">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-6 sm:px-6 lg:px-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-4">
                        <x-application-logo class="h-11 w-11 fill-current text-[color:var(--app-accent)]" />
                        <div>
                            <p class="font-display text-xl font-semibold">{{ config('app.name', 'TTRPG Social') }}</p>
                            <p class="text-xs uppercase tracking-[0.25em]" style="color: var(--app-text-muted);">{{ __('Tabletop social platform') }}</p>
                        </div>
                    </a>

                    <div class="hidden items-center gap-3 md:flex">
                        <x-locale-switcher />
                        <x-theme-toggle />
                        <a href="{{ route('campaigns.index') }}" class="theme-link text-sm font-medium transition">{{ __('Browse campaigns') }}</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="accent-button inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition">{{ __('Open dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="theme-link text-sm font-medium transition">{{ __('Log in') }}</a>
                            <a href="{{ route('register') }}" class="accent-button inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition">{{ __('Create account') }}</a>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="relative">
                <section class="mx-auto grid max-w-7xl gap-10 px-4 pb-12 pt-8 sm:px-6 lg:grid-cols-[1.1fr,0.9fr] lg:px-8 lg:pb-20 lg:pt-12">
                    <div class="max-w-2xl">
                        <p class="eyebrow">{{ __('Organize your next adventure') }}</p>
                        <h1 class="mt-6 font-display text-5xl leading-tight sm:text-6xl">{{ __('The home for campaigns, players, and every moment between sessions.') }}</h1>
                        <p class="mt-6 max-w-xl text-lg leading-8" style="color: var(--app-text-muted);">{{ __('Discover open tables, run your own campaigns, coordinate attendance, keep references in one place, and make session chat feel alive with realtime updates and dice rolls.') }}</p>

                        <div class="mt-8 flex flex-wrap items-center gap-4">
                            @auth
                                <a href="{{ route('campaigns.mine') }}" class="accent-button inline-flex items-center rounded-full px-6 py-3 text-sm font-semibold transition">{{ __('Go to My Campaigns') }}</a>
                            @else
                                <a href="{{ route('register') }}" class="accent-button inline-flex items-center rounded-full px-6 py-3 text-sm font-semibold transition">{{ __('Start for free') }}</a>
                                <a href="{{ route('login') }}" class="ghost-button px-6 py-3">{{ __('Log in') }}</a>
                            @endauth
                            <a href="{{ route('campaigns.index') }}" class="ghost-button px-6 py-3">{{ __('Browse public campaigns') }}</a>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-4">
                            <div class="surface-panel rounded-3xl px-4 py-5">
                                <p class="eyebrow text-xs">{{ __('Campaigns') }}</p>
                                <p class="mt-3 text-3xl font-semibold">{{ $highlights['campaigns'] }}</p>
                            </div>
                            <div class="surface-panel rounded-3xl px-4 py-5">
                                <p class="eyebrow text-xs">{{ __('Systems') }}</p>
                                <p class="mt-3 text-3xl font-semibold">{{ $highlights['systems'] }}</p>
                            </div>
                            <div class="surface-panel rounded-3xl px-4 py-5">
                                <p class="eyebrow text-xs">{{ __('Players') }}</p>
                                <p class="mt-3 text-3xl font-semibold">{{ $highlights['players'] }}</p>
                            </div>
                            <div class="surface-panel rounded-3xl px-4 py-5">
                                <p class="eyebrow text-xs">{{ __('Active seats') }}</p>
                                <p class="mt-3 text-3xl font-semibold">{{ $highlights['memberships'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="surface-panel overflow-hidden rounded-[2rem] p-6 sm:p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="eyebrow">{{ __('Session control') }}</p>
                                <h2 class="mt-3 font-display text-3xl">{{ __('Run the table without juggling five tools.') }}</h2>
                            </div>
                            <span class="page-chip page-chip-live">{{ __('Live') }}</span>
                        </div>

                        <div class="mt-8 space-y-4">
                            <div class="surface-panel-strong rounded-3xl p-5">
                                <p class="text-sm font-semibold">{{ __('Discover campaigns') }}</p>
                                <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Search public tables by system, status, and synopsis, then request to join the stories that fit your play style.') }}</p>
                            </div>
                            <div class="surface-panel-strong rounded-3xl p-5">
                                <p class="text-sm font-semibold">{{ __('Manage your table') }}</p>
                                <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Review join requests, invite players by username, schedule sessions, track RSVP responses, and keep campaign notes close at hand.') }}</p>
                            </div>
                            <div class="surface-panel-strong rounded-3xl p-5">
                                <p class="text-sm font-semibold">{{ __('Realtime chat and dice') }}</p>
                                <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Keep table chatter moving with campaign chat, important message notifications, and a built-in roller for quick adjudication.') }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="eyebrow">{{ __('Featured tables') }}</p>
                            <h2 class="mt-3 font-display text-3xl">{{ __('Open campaigns recruiting right now') }}</h2>
                        </div>
                        <a href="{{ route('campaigns.index') }}" class="theme-link text-sm font-semibold uppercase tracking-[0.2em] transition">{{ __('View all campaigns') }}</a>
                    </div>

                    <div class="mt-8 grid gap-6 lg:grid-cols-3">
                        @forelse($featuredCampaigns as $campaign)
                            <article class="surface-panel rounded-[2rem] p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="eyebrow text-xs">{{ $campaign->gameSystem->name }}</p>
                                        <h3 class="mt-3 font-display text-2xl">{{ $campaign->title }}</h3>
                                    </div>
                                    <span class="page-chip">{{ ucfirst($campaign->status->value) }}</span>
                                </div>

                                <p class="mt-4 text-sm leading-7" style="color: var(--app-text-muted);">{{ $campaign->synopsis }}</p>

                                <div class="mt-6 flex items-center justify-between text-sm" style="color: var(--app-text-muted);">
                                    <span>{{ __('GM') }}: {{ $campaign->owner->name }}</span>
                                    <span>{{ __('Players') }}: {{ $campaign->members_count }}/{{ $campaign->max_players }}</span>
                                </div>

                                <div class="mt-6">
                                    <a href="{{ route('campaigns.show', $campaign) }}" class="ghost-button">{{ __('View campaign') }}</a>
                                </div>
                            </article>
                        @empty
                            <div class="surface-panel rounded-[2rem] p-8 lg:col-span-3">
                                <p class="font-display text-2xl">{{ __('Campaigns are on the way') }}</p>
                                <p class="mt-3 max-w-2xl text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Seed the platform or create the first public table to make discovery feel alive for your group.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="mx-auto grid max-w-7xl gap-6 px-4 py-10 sm:px-6 lg:grid-cols-2 lg:px-8">
                    <div class="surface-panel rounded-[2rem] p-8">
                        <p class="eyebrow">{{ __('Systems and compendium') }}</p>
                        <h2 class="mt-4 font-display text-3xl">{{ __('Keep primers, links, and table lore where everyone can find them.') }}</h2>
                        <p class="mt-4 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Attach onboarding notes, session zero agreements, house rules, and reference links directly to the campaign so new players can catch up fast.') }}</p>
                    </div>

                    <div class="surface-panel rounded-[2rem] p-8">
                        <p class="eyebrow">{{ __('What groups say') }}</p>
                        <div class="mt-5 space-y-5">
                            <blockquote class="hero-subcard text-sm leading-7">
                                “{{ __('We stopped losing track of RSVPs and table notes between Discord, spreadsheets, and chat. Everything important is finally in one place.') }}”
                            </blockquote>
                            <blockquote class="hero-subcard text-sm leading-7">
                                “{{ __('The public campaign page made it much easier to recruit one more player without rewriting the pitch every week.') }}”
                            </blockquote>
                        </div>
                    </div>
                </section>

                <section class="mx-auto max-w-7xl px-4 pb-20 pt-10 sm:px-6 lg:px-8">
                    <div class="surface-panel rounded-[2.25rem] px-8 py-10 text-center sm:px-12">
                        <p class="eyebrow">{{ __('Ready to gather the party?') }}</p>
                        <h2 class="mt-4 font-display text-4xl">{{ __('Build your next campaign hub in minutes.') }}</h2>
                        <p class="mx-auto mt-4 max-w-2xl text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Create your table, invite players, manage join requests, and keep your group aligned from session zero onward.') }}</p>
                        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                            @auth
                                <a href="{{ route('campaigns.create') }}" class="accent-button inline-flex items-center rounded-full px-6 py-3 text-sm font-semibold transition">{{ __('Create campaign') }}</a>
                            @else
                                <a href="{{ route('register') }}" class="accent-button inline-flex items-center rounded-full px-6 py-3 text-sm font-semibold transition">{{ __('Create account') }}</a>
                            @endauth
                            <a href="{{ route('campaigns.index') }}" class="ghost-button px-6 py-3">{{ __('Browse campaigns') }}</a>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
