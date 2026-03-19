<div class="space-y-6">
    <div class="page-card">
        <div class="grid gap-4 md:grid-cols-5">
            <div class="md:col-span-2">
                <x-input-label for="search" :value="__('Search')" />
                <x-text-input id="search" type="text" wire:model.live.debounce.300ms="search" class="mt-1 block w-full" placeholder="{{ __('Search by title or synopsis') }}" />
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" wire:model.live="status" class="form-surface mt-1 block w-full rounded-2xl border px-4 py-3 shadow-sm focus:border-amber-400/40 focus:ring-amber-400/30">
                    <option value="">{{ __('Any') }}</option>
                    @foreach(['open', 'full', 'ongoing', 'paused', 'finished'] as $value)
                        <option value="{{ $value }}">{{ ucfirst($value) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="system" :value="__('System')" />
                <select id="system" wire:model.live="system" class="form-surface mt-1 block w-full rounded-2xl border px-4 py-3 shadow-sm focus:border-amber-400/40 focus:ring-amber-400/30">
                    <option value="">{{ __('Any') }}</option>
                    @foreach($gameSystems as $systemOption)
                        <option value="{{ $systemOption->slug }}">{{ $systemOption->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <label class="flex items-center gap-3 text-sm" style="color: var(--app-text-muted);">
                    <input type="checkbox" wire:model.live="openOnly" class="checkbox-accent rounded shadow-sm">
                    {{ __('Only open campaigns') }}
                </label>
            </div>
        </div>
    </div>

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
                        <dd class="mt-1">{{ $campaign->members_count }} / {{ $campaign->max_players }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium" style="color: var(--app-text);">{{ __('Timezone') }}</dt>
                        <dd class="mt-1">{{ $campaign->timezone }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium" style="color: var(--app-text);">{{ __('Frequency') }}</dt>
                        <dd class="mt-1">{{ $campaign->frequency_label ?: __('Flexible') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex items-center justify-end">
                    <a
                        href="{{ route('campaigns.show', $campaign) }}"
                        class="page-outline-button"
                    >
                        {{ __('View campaign') }}
                    </a>
                </div>
            </article>
        @empty
            <div class="page-card-soft border-dashed text-sm md:col-span-2 xl:col-span-3" style="color: var(--app-text-muted);">
                {{ __('No campaigns matched your filters.') }}
            </div>
        @endforelse
    </div>

    <div>
        {{ $campaigns->links() }}
    </div>
</div>
