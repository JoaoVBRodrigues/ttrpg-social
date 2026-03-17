<x-app-layout>
    @php($referenceTypes = [
        'useful_link' => __('Useful link'),
        'house_rule' => __('House rule'),
        'system_note' => __('System note'),
        'intro_material' => __('Intro material'),
        'character_baseline' => __('Character baseline'),
    ])

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

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Sessions and RSVP') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Upcoming sessions are shown in your timezone and stored internally in UTC.') }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <livewire:sessions.rsvp-panel :campaign="$campaign" />
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Campaign compendium') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Quick-reference links, notes, and onboarding material for the table.') }}</p>
                        </div>
                        <span class="text-sm text-slate-500">{{ $campaign->references->count() }} {{ __('entries') }}</span>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse($campaign->references as $reference)
                            <article class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                @can('update', $campaign)
                                    <form method="POST" action="{{ route('campaign-references.update', $reference) }}" class="space-y-4">
                                        @csrf
                                        @method('PUT')

                                        <div class="grid gap-4 md:grid-cols-2">
                                            <div>
                                                <x-input-label for="{{ 'reference_title_'.$reference->id }}" :value="__('Title')" />
                                                <x-text-input id="{{ 'reference_title_'.$reference->id }}" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $reference->title) }}" required />
                                            </div>
                                            <div>
                                                <x-input-label for="{{ 'reference_type_'.$reference->id }}" :value="__('Type')" />
                                                <select id="{{ 'reference_type_'.$reference->id }}" name="type" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    @foreach($referenceTypes as $value => $label)
                                                        <option value="{{ $value }}" @selected(old('type', $reference->type) === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <x-input-label for="{{ 'reference_content_'.$reference->id }}" :value="__('Content')" />
                                            <textarea id="{{ 'reference_content_'.$reference->id }}" name="content" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content', $reference->content) }}</textarea>
                                        </div>

                                        <div class="grid gap-4 md:grid-cols-[2fr,1fr]">
                                            <div>
                                                <x-input-label for="{{ 'reference_url_'.$reference->id }}" :value="__('External URL')" />
                                                <x-text-input id="{{ 'reference_url_'.$reference->id }}" name="external_url" type="url" class="mt-1 block w-full" value="{{ old('external_url', $reference->external_url) }}" />
                                            </div>
                                            <div>
                                                <x-input-label for="{{ 'reference_sort_'.$reference->id }}" :value="__('Sort order')" />
                                                <x-text-input id="{{ 'reference_sort_'.$reference->id }}" name="sort_order" type="number" min="0" class="mt-1 block w-full" value="{{ old('sort_order', $reference->sort_order) }}" />
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between gap-4">
                                            <p class="text-sm text-slate-500">
                                                {{ $referenceTypes[$reference->type] ?? ucfirst(str_replace('_', ' ', $reference->type)) }}
                                                @if($reference->creator)
                                                    {{ __('by') }} {{ $reference->creator->name }}
                                                @endif
                                            </p>

                                            <x-primary-button>{{ __('Save entry') }}</x-primary-button>
                                        </div>
                                    </form>

                                    <form method="POST" action="{{ route('campaign-references.destroy', $reference) }}" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-rose-600 transition hover:text-rose-700">
                                            {{ __('Delete entry') }}
                                        </button>
                                    </form>
                                @else
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h4 class="text-base font-semibold text-slate-900">{{ $reference->title }}</h4>
                                            <p class="mt-1 text-sm uppercase tracking-[0.2em] text-slate-500">{{ $referenceTypes[$reference->type] ?? ucfirst(str_replace('_', ' ', $reference->type)) }}</p>
                                        </div>

                                        @if($reference->external_url)
                                            <a href="{{ $reference->external_url }}" target="_blank" rel="noreferrer" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-700">
                                                {{ __('Open link') }}
                                            </a>
                                        @endif
                                    </div>

                                    @if($reference->content)
                                        <p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $reference->content }}</p>
                                    @endif
                                @endcan
                            </article>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-6 text-sm text-slate-500">
                                {{ __('No compendium entries yet.') }}
                            </div>
                        @endforelse
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
                                    <p class="text-sm text-slate-500">{{ '@'.$member->user->username }} - {{ $member->role->value }} - {{ $member->status->value }}</p>
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

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Campaign chat') }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Messages and dice rolls are persisted and broadcast to active members in realtime.') }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <livewire:chat.campaign-chat :campaign="$campaign" />
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
                    @php($isActiveMember = auth()->user()->campaignMemberships()->where('campaign_id', $campaign->id)->where('status', 'active')->exists())

                    @if($isActiveMember)
                        <form method="POST" action="{{ route('campaigns.messages.store', $campaign) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            @csrf
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Post a message') }}</h3>
                            <div class="mt-4">
                                <x-input-label for="message_content" :value="__('Message')" />
                                <textarea id="message_content" name="content" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                            </div>
                            <label for="message_is_important" class="mt-4 flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                <input id="message_is_important" name="is_important" type="checkbox" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span>{{ __('Mark as important to notify members using their message notification preferences.') }}</span>
                            </label>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Send message') }}</x-primary-button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('campaigns.rolls.store', $campaign) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            @csrf
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Roll dice') }}</h3>
                            <div class="mt-4">
                                <x-input-label for="dice_expression" :value="__('Expression')" />
                                <x-text-input id="dice_expression" name="expression" type="text" class="mt-1 block w-full" placeholder="1d20+4 or 1d20 adv" required />
                            </div>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Roll now') }}</x-primary-button>
                            </div>
                        </form>
                    @endif

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
                        <form method="POST" action="{{ route('campaign-sessions.store', $campaign) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            @csrf
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Schedule a session') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <x-input-label for="session_title" :value="__('Title')" />
                                    <x-text-input id="session_title" name="title" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="session_description" :value="__('Description')" />
                                    <textarea id="session_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <div>
                                    <x-input-label for="session_starts_at" :value="__('Starts at')" />
                                    <x-text-input id="session_starts_at" name="starts_at" type="datetime-local" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="session_ends_at" :value="__('Ends at')" />
                                    <x-text-input id="session_ends_at" name="ends_at" type="datetime-local" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="session_timezone" :value="__('Timezone')" />
                                    <x-text-input id="session_timezone" name="timezone" type="text" class="mt-1 block w-full" value="{{ $campaign->timezone }}" required />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Create session') }}</x-primary-button>
                            </div>
                        </form>

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

                        <form method="POST" action="{{ route('campaigns.references.store', $campaign) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                            @csrf
                            <h3 class="text-lg font-medium text-slate-900">{{ __('Add compendium entry') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <x-input-label for="reference_title" :value="__('Title')" />
                                    <x-text-input id="reference_title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                </div>
                                <div>
                                    <x-input-label for="reference_type" :value="__('Type')" />
                                    <select id="reference_type" name="type" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        @foreach($referenceTypes as $value => $label)
                                            <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                                </div>
                                <div>
                                    <x-input-label for="reference_content" :value="__('Content')" />
                                    <textarea id="reference_content" name="content" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content') }}</textarea>
                                    <x-input-error class="mt-2" :messages="$errors->get('content')" />
                                </div>
                                <div>
                                    <x-input-label for="reference_external_url" :value="__('External URL')" />
                                    <x-text-input id="reference_external_url" name="external_url" type="url" class="mt-1 block w-full" value="{{ old('external_url') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('external_url')" />
                                </div>
                                <div>
                                    <x-input-label for="reference_sort_order" :value="__('Sort order')" />
                                    <x-text-input id="reference_sort_order" name="sort_order" type="number" min="0" class="mt-1 block w-full" value="{{ old('sort_order') }}" />
                                    <x-input-error class="mt-2" :messages="$errors->get('sort_order')" />
                                </div>
                            </div>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Save reference') }}</x-primary-button>
                            </div>
                        </form>
                    @endcan
                @endauth
            </aside>
        </div>
    </div>
</x-app-layout>
