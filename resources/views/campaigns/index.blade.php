<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">{{ __('Discover campaigns') }}</p>
                <h2 class="mt-3 font-display text-3xl leading-tight">{{ __('Campaigns') }}</h2>
                <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Discover public tables, filter by system, and find your next session.') }}</p>
            </div>

            @auth
                <a href="{{ route('campaigns.create') }}" class="accent-button inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition">
                    {{ __('Create campaign') }}
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="page-stack mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <livewire:campaigns.campaign-list />
        </div>
    </div>
</x-app-layout>
