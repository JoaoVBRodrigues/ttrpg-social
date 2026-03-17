<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">{{ $campaign->gameSystem->name }}</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $campaign->title }}</h2>
            </div>

            @can('update', $campaign)
                <a href="{{ route('campaigns.edit', $campaign) }}" class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                    {{ __('Edit campaign') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[2fr,1fr] lg:px-8">
            <section class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <p class="text-sm leading-7 text-slate-600">{{ $campaign->synopsis }}</p>
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        <div>
                            <h3 class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">{{ __('Description') }}</h3>
                            <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $campaign->description ?: __('No full description yet.') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500">{{ __('House rules') }}</h3>
                            <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $campaign->rules_summary ?: __('No house rules provided yet.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Members') }}</h3>
                        <span class="text-sm text-slate-500">{{ $campaign->members_count }} {{ __('tracked memberships') }}</span>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach($campaign->members as $member)
                            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $member->user->name }}</p>
                                    <p class="text-sm text-slate-500">{{ '@'.$member->user->username }} · {{ $member->role->value }} · {{ $member->status->value }}</p>
                                </div>

                                @can('manageMembers', $campaign)
                                    @if(in_array($member->status->value, ['pending', 'invited'], true))
                                        <form method="POST" action="{{ route('campaign-members.review', $member) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="active">
                                            <x-primary-button>{{ __('Approve') }}</x-primary-button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-medium text-slate-900">{{ __('Campaign details') }}</h3>
                    <dl class="mt-4 space-y-4 text-sm text-slate-600">
                        <div>
                            <dt class="font-medium text-slate-900">{{ __('GM') }}</dt>
                            <dd class="mt-1">{{ $campaign->owner->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-900">{{ __('Visibility') }}</dt>
                            <dd class="mt-1">{{ ucfirst($campaign->visibility->value) }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-slate-900">{{ __('Status') }}</dt>
                            <dd class="mt-1">{{ ucfirst($campaign->status->value) }}</dd>
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
                </div>

                @auth
                    @can('requestJoin', $campaign)
                        <form method="POST" action="{{ route('campaigns.members.request', $campaign) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            @csrf
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Join this table') }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ __('Send a join request to the game master.') }}</p>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Request to join') }}</x-primary-button>
                            </div>
                        </form>
                    @endcan

                    @can('manageMembers', $campaign)
                        <form method="POST" action="{{ route('campaigns.members.invite', $campaign) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            @csrf
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Invite a member') }}</h3>
                            <div class="mt-4">
                                <x-input-label for="username" :value="__('Username')" />
                                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('username')" />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="player">{{ __('Player') }}</option>
                                    <option value="spectator">{{ __('Spectator') }}</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Send invite') }}</x-primary-button>
                            </div>
                        </form>
                    @endcan
                @endauth
            </aside>
        </div>
    </div>
</x-app-layout>
