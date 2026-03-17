<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $profileUser->name }} · {{ config('app.name', 'TTRPG Social') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
        @php($profile = $profileResource->resolve(request()))

        <div class="mx-auto flex min-h-screen max-w-5xl flex-col px-4 py-10 sm:px-6 lg:px-8">
            <header class="flex items-center justify-between gap-4">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="text-sm font-medium text-slate-500 transition hover:text-slate-900">
                    {{ __('Back') }}
                </a>

                @auth
                    @if (auth()->id() === $profileUser->id)
                        <a href="{{ route('profile') }}" class="inline-flex items-center rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            {{ __('Edit profile') }}
                        </a>
                    @endif
                @endauth
            </header>

            <main class="mt-8 flex-1 space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-sm font-medium uppercase tracking-[0.3em] text-slate-500">{{ __('TTRPG profile') }}</p>
                    <div class="mt-4 grid gap-8 md:grid-cols-[1.5fr,1fr]">
                        <div>
                            <h1 class="text-3xl font-semibold text-slate-900">{{ $profile['name'] }}</h1>
                            <p class="mt-2 text-sm text-slate-500">{{ '@'.$profile['username'] }}</p>
                            <p class="mt-6 whitespace-pre-line text-sm leading-7 text-slate-600">
                                {{ $profile['bio'] ?: __('This user has not added a bio yet.') }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-6">
                            <h2 class="text-lg font-medium text-slate-900">{{ __('Preferences') }}</h2>
                            <dl class="mt-4 space-y-4 text-sm text-slate-600">
                                <div>
                                    <dt class="font-medium text-slate-900">{{ __('Preferred role') }}</dt>
                                    <dd class="mt-1">{{ ucfirst(str_replace('_', ' ', $profile['preferred_role'])) }}</dd>
                                </div>
                                <div>
                                    <dt class="font-medium text-slate-900">{{ __('Timezone') }}</dt>
                                    <dd class="mt-1">{{ $profile['timezone'] }}</dd>
                                </div>
                                @if ($profile['email'])
                                    <div>
                                        <dt class="font-medium text-slate-900">{{ __('Email') }}</dt>
                                        <dd class="mt-1">{{ $profile['email'] }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </section>

                <section class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <h2 class="text-lg font-medium text-slate-900">{{ __('Favorite systems') }}</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @forelse ($profile['favorite_systems'] as $system)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700">{{ $system }}</span>
                            @empty
                                <p class="text-sm text-slate-500">{{ __('No favorite systems listed yet.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                        <h2 class="text-lg font-medium text-slate-900">{{ __('Availability') }}</h2>
                        <div class="mt-4 space-y-3">
                            @forelse ($profile['availability'] as $slot)
                                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700">
                                    <span class="font-medium text-slate-900">{{ $slot['day'] ?? __('Day') }}</span>
                                    <span class="ml-2">{{ $slot['window'] ?? '' }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">{{ __('No availability shared yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
