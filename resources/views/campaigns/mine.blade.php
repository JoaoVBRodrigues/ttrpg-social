<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('My Campaigns') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('Campaigns you run or actively belong to appear here.') }}</p>
            </div>

            <a href="{{ route('campaigns.create') }}" class="inline-flex items-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
                {{ __('Create campaign') }}
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('campaigns.mine') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="grid gap-4 md:grid-cols-4">
                    <div class="md:col-span-3">
                        <x-input-label for="my_campaigns_search" :value="__('Search')" />
                        <x-text-input id="my_campaigns_search" name="search" type="text" class="mt-1 block w-full" value="{{ $filters['search'] }}" placeholder="{{ __('Search by title or synopsis') }}" />
                    </div>

                    <div>
                        <x-input-label for="my_campaigns_status" :value="__('Status')" />
                        <select id="my_campaigns_status" name="status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Any') }}</option>
                            @foreach(['open', 'full', 'ongoing', 'paused', 'finished'] as $value)
                                <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ ucfirst($value) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-end gap-3">
                    @if($filters['search'] || $filters['status'])
                        <a href="{{ route('campaigns.mine') }}" class="text-sm font-medium text-slate-600 transition hover:text-slate-900">
                            {{ __('Clear filters') }}
                        </a>
                    @endif

                    <x-primary-button>{{ __('Apply filters') }}</x-primary-button>
                </div>
            </form>

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
                                <dd class="mt-1">{{ $campaign->active_members_count }} / {{ $campaign->max_players }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-slate-900">{{ __('Timezone') }}</dt>
                                <dd class="mt-1">{{ $campaign->timezone }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-slate-900">{{ __('Role here') }}</dt>
                                <dd class="mt-1">{{ $campaign->owner_id === auth()->id() ? __('GM') : __('Player') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                                {{ __('Open table') }}
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-10 text-sm text-slate-500 md:col-span-2 xl:col-span-3">
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
