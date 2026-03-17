<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Campaigns') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Discover public tables, filter by system, and find your next session.') }}</p>
            </div>

            @auth
                <a href="{{ route('campaigns.create') }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                    {{ __('Create campaign') }}
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <livewire:campaigns.campaign-list />
        </div>
    </div>
</x-app-layout>
