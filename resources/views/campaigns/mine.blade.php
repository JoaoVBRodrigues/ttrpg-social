<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">{{ __('Your active tables') }}</p>
                <h2 class="mt-3 font-display text-3xl leading-tight">{{ __('My Campaigns') }}</h2>
                <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Campaigns you run or actively belong to appear here.') }}</p>
            </div>

            <a href="{{ route('campaigns.create') }}" class="accent-button inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition">
                {{ __('Create campaign') }}
            </a>
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="page-stack mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('campaigns.mine') }}" class="page-card">
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <x-input-label for="my_campaigns_search" :value="__('Search')" />
                        <x-text-input id="my_campaigns_search" name="search" type="text" class="mt-1 block w-full" value="{{ $filters['search'] }}" placeholder="{{ __('Search by title or synopsis') }}" />
                    </div>

                    <div>
                        <x-input-label for="my_campaigns_status" :value="__('Status')" />
                        <select id="my_campaigns_status" name="status" class="form-surface mt-1 block w-full rounded-2xl border px-4 py-3 shadow-sm">
                            <option value="">{{ __('Any') }}</option>
                            @foreach(['open', 'full', 'ongoing', 'paused', 'finished'] as $value)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ ucfirst($value) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3">
                    @if($filters['search'] || $filters['status'])
                        <a href="{{ route('campaigns.mine') }}" class="page-link text-sm font-medium transition">
                            {{ __('Clear filters') }}
                        </a>
                    @endif

                    <x-primary-button>{{ __('Apply filters') }}</x-primary-button>
                </div>
            </form>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse($campaigns as $campaign)
                    <article class="page-card">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-[0.2em]" style="color: var(--app-text-muted);">{{ $campaign->gameSystem->name }}</p>
                                <h3 class="mt-2 font-display text-2xl">
                                    <a href="{{ route('campaigns.show', $campaign) }}" class="page-link transition">
                                        {{ $campaign->title }}
                                    </a>
                                </h3>
                            </div>
                            <span class="page-chip">{{ ucfirst($campaign->status->value) }}</span>
                        </div>

                        <p class="mt-4 text-sm leading-7" style="color: var(--app-text-muted);">{{ $campaign->synopsis }}</p>

                        <dl class="mt-6 grid grid-cols-2 gap-4 text-sm" style="color: var(--app-text-muted);">
                            <div>
                                <dt class="font-medium" style="color: var(--app-text);">{{ __('GM') }}</dt>
                                <dd class="mt-1">{{ $campaign->owner->name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium" style="color: var(--app-text);">{{ __('Players') }}</dt>
                                <dd class="mt-1">{{ $campaign->active_members_count }} / {{ $campaign->max_players }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium" style="color: var(--app-text);">{{ __('Timezone') }}</dt>
                                <dd class="mt-1">{{ $campaign->timezone }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium" style="color: var(--app-text);">{{ __('Role here') }}</dt>
                                <dd class="mt-1">{{ $campaign->owner_id === auth()->id() ? __('GM') : __('Player') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="page-outline-button">
                                {{ __('Open table') }}
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="page-card-soft border-dashed text-sm md:col-span-2 xl:col-span-3" style="color: var(--app-text-muted);">
                        {{ __('You do not belong to any campaigns yet.') }}
                    </div>
                @endforelse
            </div>

            <div>
                {{ $campaigns->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
