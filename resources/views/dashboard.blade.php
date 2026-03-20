<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">{{ __('Tabletop control center') }}</p>
                <h2 class="mt-3 font-display text-3xl leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-2 max-w-2xl text-sm leading-7" style="color: var(--app-text-muted);">
                    {{ __('Track your tables, keep your next session moving, and jump straight into campaign work.') }}
                </p>
            </div>
            <a href="{{ route('campaigns.create') }}" class="accent-button inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition">
                {{ __('Create campaign') }}
            </a>
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="page-stack mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section class="grid gap-6 lg:grid-cols-[1.2fr,0.8fr]">
                <div class="page-card">
                    <span class="page-chip">{{ __('Ready for session zero') }}</span>
                    <p class="mt-5 max-w-2xl text-base leading-8" style="color: var(--app-text-muted);">
                        {{ __("You're logged in and ready to organize your next table.") }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('campaigns.index') }}" class="accent-button inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition">
                            {{ __('Browse campaigns') }}
                        </a>
                        <a href="{{ route('campaigns.mine') }}" class="page-outline-button">
                            {{ __('My Campaigns') }}
                        </a>
                    </div>
                </div>

                <div class="page-card-soft">
                    <p class="eyebrow">{{ __('Quick route') }}</p>
                    <div class="mt-5 space-y-4 text-sm leading-7" style="color: var(--app-text-muted);">
                        <p>{{ __('Browse public tables to discover new groups, or head to My Campaigns to manage the tables you already run.') }}</p>
                        <p>{{ __('From any campaign room you can review join requests, schedule sessions, update the compendium, chat in realtime, and roll dice.') }}</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
