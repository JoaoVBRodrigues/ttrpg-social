<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="eyebrow">{{ __('Build your table') }}</p>
            <h2 class="mt-3 font-display text-3xl leading-tight">{{ __('Create campaign') }}</h2>
            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Set the tone, the system, and the table expectations before your first session.') }}</p>
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
