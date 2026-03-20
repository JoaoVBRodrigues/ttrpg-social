<x-app-layout>
    @php($referenceTypes = [
        'useful_link' => __('Useful link'),
        'house_rule' => __('House rule'),
        'system_note' => __('System note'),
        'intro_material' => __('Intro material'),
        'character_baseline' => __('Character baseline'),
    ])
    @php($viewerMembership = auth()->check() ? $campaign->members->firstWhere('user_id', auth()->id()) : null)
    @php($pendingRequests = $campaign->members->where('status', \App\Enums\CampaignMemberStatus::PENDING))
    @php($canManageMembers = auth()->check() && auth()->user()->can('manageMembers', $campaign))
    @php($visibleMembers = $canManageMembers
        ? $campaign->members
        : $campaign->members->filter(fn ($member) => $member->status === \App\Enums\CampaignMemberStatus::ACTIVE))

    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="eyebrow">{{ $campaign->gameSystem->name }}</p>
                <h2 class="mt-3 font-display text-4xl leading-tight">{{ $campaign->title }}</h2>
                <p class="mt-2 max-w-3xl text-sm leading-7" style="color: var(--app-text-muted);">{{ $campaign->synopsis }}</p>
            </div>

            @can('update', $campaign)
                <a href="{{ route('campaigns.edit', $campaign) }}" class="page-outline-button">
                    {{ __('Edit campaign') }}
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="page-stack mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[2fr,1fr] lg:px-8">
            <section class="space-y-6">
                @if (session('status'))
                    <div class="callout callout-success px-6 py-4">
                        @switch(session('status'))
                            @case('campaign-join-requested')
                                {{ __('Your join request has been sent to the GM.') }}
                                @break
                            @case('campaign-member-invited')
                                {{ __('The invitation has been sent.') }}
                                @break
                            @case('campaign-member-reviewed')
                                {{ __('The membership request has been reviewed.') }}
                                @break
                            @default
                                {{ __('The campaign has been updated.') }}
                        @endswitch
                    </div>
                @endif

                <div class="page-card">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <h3 class="text-sm font-medium uppercase tracking-[0.2em]" style="color: var(--app-text-muted);">{{ __('Description') }}</h3>
                            <p class="mt-2 whitespace-pre-line text-sm leading-7" style="color: var(--app-text-muted);">{{ $campaign->description ?: __('No full description yet.') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium uppercase tracking-[0.2em]" style="color: var(--app-text-muted);">{{ __('House rules') }}</h3>
                            <p class="mt-2 whitespace-pre-line text-sm leading-7" style="color: var(--app-text-muted);">{{ $campaign->rules_summary ?: __('No house rules provided yet.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="page-card-soft">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-display text-2xl">{{ __('Sessions and RSVP') }}</h3>
                            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Upcoming sessions are shown in your timezone and stored internally in UTC.') }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <livewire:sessions.rsvp-panel :campaign="$campaign" />
                    </div>
                </div>

                <div class="page-card">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-display text-2xl">{{ __('Campaign compendium') }}</h3>
                            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Quick-reference links, notes, and onboarding material for the table.') }}</p>
                        </div>
                        <span class="text-sm" style="color: var(--app-text-muted);">{{ $campaign->references->count() }} {{ __('entries') }}</span>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse($campaign->references as $reference)
                            <article class="page-card-soft !p-5">
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
                                                <select id="{{ 'reference_type_'.$reference->id }}" name="type" class="form-surface mt-1 block w-full rounded-2xl border px-4 py-3 shadow-sm">
                                                    @foreach($referenceTypes as $value => $label)
                                                        <option value="{{ $value }}" @selected(old('type', $reference->type) === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <x-input-label for="{{ 'reference_content_'.$reference->id }}" :value="__('Content')" />
                                            <textarea id="{{ 'reference_content_'.$reference->id }}" name="content" rows="4" class="form-surface mt-1 block w-full rounded-[1.5rem] border px-4 py-3 shadow-sm">{{ old('content', $reference->content) }}</textarea>
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
                                            <p class="text-sm" style="color: var(--app-text-muted);">
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
                                        <button type="submit" class="link-danger text-sm font-medium transition">
                                            {{ __('Delete entry') }}
                                        </button>
                                    </form>
                                @else
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h4 class="text-base font-semibold" style="color: var(--app-text);">{{ $reference->title }}</h4>
                                            <p class="mt-1 text-sm uppercase tracking-[0.2em]" style="color: var(--app-text-muted);">{{ $referenceTypes[$reference->type] ?? ucfirst(str_replace('_', ' ', $reference->type)) }}</p>
                                        </div>

                                        @if($reference->external_url)
                                            <a href="{{ $reference->external_url }}" target="_blank" rel="noreferrer" class="page-link text-sm font-medium transition">
                                                {{ __('Open link') }}
                                            </a>
                                        @endif
                                    </div>

                                    @if($reference->content)
                                        <p class="mt-4 whitespace-pre-line text-sm leading-7" style="color: var(--app-text-muted);">{{ $reference->content }}</p>
                                    @endif
                                @endcan
                            </article>
                        @empty
                            <div class="page-card-soft border-dashed px-5 py-6 text-sm" style="color: var(--app-text-muted);">
                                {{ __('No compendium entries yet.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                @if($canManageMembers)
                    <div class="page-card-soft" style="background: var(--color-warning-surface); border-color: var(--color-warning-border);">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <h3 class="font-display text-2xl">{{ __('Pending join requests') }}</h3>
                                <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Review players who requested access to your table.') }}</p>
                            </div>
                            <span class="page-chip">{{ $pendingRequests->count() }}</span>
                        </div>

                        <div class="mt-6 space-y-4">
                            @forelse($pendingRequests as $member)
                                <article class="page-card !p-5">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                                        <div>
                                            <p class="text-base font-semibold" style="color: var(--app-text);">{{ $member->user->name }}</p>
                                            <p class="mt-1 text-sm" style="color: var(--app-text-muted);">{{ '@'.$member->user->username }} · {{ __('Requested to join this campaign') }}</p>
                                        </div>

                                        <form method="POST" action="{{ route('campaign-members.review', $member) }}" class="w-full max-w-xl space-y-4">
                                            @csrf
                                            @method('PATCH')

                                            <div>
                                                <x-input-label for="{{ 'review_message_'.$member->id }}" :value="__('Optional message')" />
                                                <textarea id="{{ 'review_message_'.$member->id }}" name="message" rows="3" class="form-surface mt-1 block w-full rounded-[1.5rem] border px-4 py-3 shadow-sm" placeholder="{{ __('Share a short approval note or denial reason.') }}">{{ old('message') }}</textarea>
                                            </div>

                                            <div class="flex flex-wrap gap-3">
                                                <button type="submit" name="status" value="active" class="accent-button inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition">
                                                    {{ __('Approve request') }}
                                                </button>
                                                <button type="submit" name="status" value="rejected" class="page-outline-button outline-danger">
                                                    {{ __('Deny request') }}
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </article>
                            @empty
                                <div class="page-card border-dashed px-5 py-6 text-sm" style="color: var(--app-text-muted);">
                                    {{ __('No pending join requests right now.') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                <div class="page-card">
                    <div class="flex items-center justify-between">
                        <h3 class="font-display text-2xl">{{ __('Members') }}</h3>
                        <span class="text-sm" style="color: var(--app-text-muted);">{{ $visibleMembers->count() }} {{ __('visible memberships') }}</span>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach($visibleMembers as $member)
                            <div class="page-card-soft !p-4 flex items-center justify-between">
                                <div>
                                    <p class="font-medium" style="color: var(--app-text);">{{ $member->user->name }}</p>
                                    <p class="text-sm" style="color: var(--app-text-muted);">{{ '@'.$member->user->username }} - {{ $member->role->value }} - {{ $member->status->value }}</p>
                                </div>

                                @if($canManageMembers)
                                    @if(in_array($member->status->value, ['pending', 'invited'], true))
                                        <form method="POST" action="{{ route('campaign-members.review', $member) }}" class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="active">
                                            <x-primary-button>{{ __('Approve') }}</x-primary-button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="page-card-soft">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-display text-2xl">{{ __('Campaign chat') }}</h3>
                            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Messages and dice rolls are persisted and broadcast to active members in realtime.') }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <livewire:chat.campaign-chat :campaign="$campaign" />
                    </div>
                </div>
            </section>

            <aside class="space-y-6">
                <div class="page-card">
                    <h3 class="font-display text-2xl">{{ __('Campaign details') }}</h3>
                    <dl class="mt-4 space-y-4 text-sm" style="color: var(--app-text-muted);">
                        <div>
                            <dt class="font-medium" style="color: var(--app-text);">{{ __('GM') }}</dt>
                            <dd class="mt-1">{{ $campaign->owner->name }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium" style="color: var(--app-text);">{{ __('Visibility') }}</dt>
                            <dd class="mt-1">{{ ucfirst($campaign->visibility->value) }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium" style="color: var(--app-text);">{{ __('Status') }}</dt>
                            <dd class="mt-1">{{ ucfirst($campaign->status->value) }}</dd>
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
                </div>

                @auth
                    @php($isActiveMember = $viewerMembership?->status === \App\Enums\CampaignMemberStatus::ACTIVE)

                    @if($isActiveMember)
                        <form method="POST" action="{{ route('campaigns.rolls.store', $campaign) }}" class="page-card">
                            @csrf
                            <h3 class="font-display text-2xl">{{ __('Roll dice') }}</h3>
                            <div class="mt-4">
                                <x-input-label for="dice_expression" :value="__('Expression')" />
                                <x-text-input id="dice_expression" name="expression" type="text" class="mt-1 block w-full" placeholder="1d20+4 or 1d20 adv" required />
                            </div>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Roll now') }}</x-primary-button>
                            </div>
                        </form>
                    @endif

                    @if($viewerMembership?->status === \App\Enums\CampaignMemberStatus::PENDING)
                        <div class="callout callout-warning rounded-[2rem] p-6">
                            <h3 class="font-display text-2xl">{{ __('Join request pending') }}</h3>
                            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Your request is waiting for GM review.') }}</p>
                        </div>
                    @elseif($viewerMembership?->status === \App\Enums\CampaignMemberStatus::REJECTED)
                        <div class="callout callout-danger rounded-[2rem] p-6">
                            <h3 class="font-display text-2xl">{{ __('Join request declined') }}</h3>
                            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('The GM declined your request for this campaign.') }}</p>
                            @if($viewerMembership->review_message)
                                <div class="page-card mt-4 !p-4 text-sm" style="color: var(--app-text);">
                                    <span class="font-medium">{{ __('GM note:') }}</span>
                                    {{ $viewerMembership->review_message }}
                                </div>
                            @endif
                        </div>
                    @elseif($viewerMembership?->status === \App\Enums\CampaignMemberStatus::INVITED)
                        <div class="callout callout-info rounded-[2rem] p-6">
                            <h3 class="font-display text-2xl">{{ __('Invitation pending') }}</h3>
                            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('You have been invited to this campaign and are waiting for GM confirmation.') }}</p>
                        </div>
                    @elseif($viewerMembership?->status === \App\Enums\CampaignMemberStatus::ACTIVE)
                        <div class="callout callout-success rounded-[2rem] p-6">
                            <h3 class="font-display text-2xl">{{ __('You are part of this table') }}</h3>
                            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('You already belong to this campaign and can access member-only tools below.') }}</p>
                        </div>
                    @elseif(auth()->user()->can('requestJoin', $campaign))
                        <form method="POST" action="{{ route('campaigns.members.request', $campaign) }}" class="page-card">
                            @csrf
                            <h3 class="font-display text-2xl">{{ __('Join this table') }}</h3>
                            <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">{{ __('Send a join request to the game master.') }}</p>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Request to join') }}</x-primary-button>
                            </div>
                        </form>
                    @endif

                    @can('manageMembers', $campaign)
                        <form method="POST" action="{{ route('campaign-sessions.store', $campaign) }}" class="page-card">
                            @csrf
                            <h3 class="font-display text-2xl">{{ __('Schedule a session') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <x-input-label for="session_title" :value="__('Title')" />
                                    <x-text-input id="session_title" name="title" type="text" class="mt-1 block w-full" required />
                                </div>
                                <div>
                                    <x-input-label for="session_description" :value="__('Description')" />
                                    <textarea id="session_description" name="description" rows="3" class="form-surface mt-1 block w-full rounded-[1.5rem] border px-4 py-3 shadow-sm"></textarea>
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

                        <form method="POST" action="{{ route('campaigns.members.invite', $campaign) }}" class="page-card">
                            @csrf
                            <h3 class="font-display text-2xl">{{ __('Invite a member') }}</h3>
                            <div class="mt-4">
                                <x-input-label for="username" :value="__('Username')" />
                                <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('username')" />
                            </div>
                            <div class="mt-4">
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="form-surface mt-1 block w-full rounded-2xl border px-4 py-3 shadow-sm">
                                    <option value="player">{{ __('Player') }}</option>
                                    <option value="spectator">{{ __('Spectator') }}</option>
                                </select>
                            </div>
                            <div class="mt-4">
                                <x-primary-button>{{ __('Send invite') }}</x-primary-button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('campaigns.references.store', $campaign) }}" class="page-card">
                            @csrf
                            <h3 class="font-display text-2xl">{{ __('Add compendium entry') }}</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <x-input-label for="reference_title" :value="__('Title')" />
                                    <x-text-input id="reference_title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title') }}" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                </div>
                                <div>
                                    <x-input-label for="reference_type" :value="__('Type')" />
                                    <select id="reference_type" name="type" class="form-surface mt-1 block w-full rounded-2xl border px-4 py-3 shadow-sm" required>
                                        @foreach($referenceTypes as $value => $label)
                                            <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('type')" />
                                </div>
                                <div>
                                    <x-input-label for="reference_content" :value="__('Content')" />
                                    <textarea id="reference_content" name="content" rows="4" class="form-surface mt-1 block w-full rounded-[1.5rem] border px-4 py-3 shadow-sm">{{ old('content') }}</textarea>
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
