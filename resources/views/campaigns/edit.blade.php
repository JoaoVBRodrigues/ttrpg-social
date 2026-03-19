<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-500">{{ __('Refine your pitch') }}</p>
            <h2 class="mt-3 font-display text-3xl leading-tight">{{ __('Edit campaign') }}</h2>
            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Keep the table details, recruiting message, and play expectations aligned for prospective players.') }}</p>
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="page-stack mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="page-card">
                @include('campaigns._form')
            </div>
        </div>
    </div>
</x-app-layout>
