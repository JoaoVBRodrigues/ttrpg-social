<div class="space-y-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 md:grid-cols-5">
            <div class="md:col-span-2">
                <x-input-label for="search" :value="__('Search')" />
                <x-text-input id="search" type="text" wire:model.live.debounce.300ms="search" class="mt-1 block w-full" placeholder="{{ __('Search by title or synopsis') }}" />
            </div>

            <div>
                <x-input-label for="status" :value="__('Status')" />
                <select id="status" wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Any') }}</option>
                    @foreach(['open', 'full', 'ongoing', 'paused', 'finished'] as $value)
                        <option value="{{ $value }}">{{ ucfirst($value) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="system" :value="__('System')" />
                <select id="system" wire:model.live="system" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Any') }}</option>
                    @foreach($gameSystems as $systemOption)
                        <option value="{{ $systemOption->slug }}">{{ $systemOption->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <label class="flex items-center gap-3 text-sm text-slate-600">
                    <input type="checkbox" wire:model.live="openOnly" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    {{ __('Only open campaigns') }}
                </label>
            </div>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse($campaigns as $campaign)
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500">{{ $campaign->gameSystem->name }}</p>
                        <h3 class="mt-2 text-xl font-semibold text-slate-900">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="transition hover:text-indigo-600">
                                {{ $campaign->title }}
                            </a>
                        </h3>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">{{ ucfirst($campaign->status->value) }}</span>
                </div>

                <p class="mt-4 text-sm leading-6 text-slate-600">{{ $campaign->synopsis }}</p>

                <dl class="mt-6 grid grid-cols-2 gap-4 text-sm text-slate-600">
                    <div>
                        <dt class="font-medium text-slate-900">{{ __('GM') }}</dt>
                        <dd class="mt-1">{{ $campaign->owner->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900">{{ __('Players') }}</dt>
                        <dd class="mt-1">{{ $campaign->members_count }} / {{ $campaign->max_players }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900">{{ __('Timezone') }}</dt>
                        <dd class="mt-1">{{ $campaign->timezone }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-slate-900">{{ __('Frequency') }}</dt>
                        <dd class="mt-1">{{ $campaign->frequency_label ?: __('Flexible') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex items-center justify-end">
                    <a
                        href="{{ route('campaigns.show', $campaign) }}"
                        class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900"
                    >
                        {{ __('View campaign') }}
                    </a>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-sm text-slate-500 md:col-span-2 xl:col-span-3">
                {{ __('No campaigns matched your filters.') }}
            </div>
        @endforelse
    </div>

    <div>
        {{ $campaigns->links() }}
    </div>
</div>
