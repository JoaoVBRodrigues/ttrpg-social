<div class="space-y-4">
    @forelse($sessions as $session)
        @php($attendance = $session->attendances->first())
        <article class="page-card-soft !p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h4 class="font-display text-2xl">{{ $session->title }}</h4>
                    <p class="mt-1 text-sm" style="color: var(--app-text-muted);">{{ $session->starts_at?->timezone(auth()->user()?->timezone ?? $session->timezone)->format('M d, Y H:i') }} · {{ $session->timezone }}</p>
                </div>
                <span class="page-chip">{{ ucfirst($session->status->value) }}</span>
            </div>

            @if($session->description)
                <p class="mt-4 text-sm leading-7" style="color: var(--app-text-muted);">{{ $session->description }}</p>
            @endif

            @auth
                <form method="POST" action="{{ route('campaign-sessions.attendance.update', $session) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label :for="'status-'.$session->id" :value="__('RSVP')" />
                        <select id="status-{{ $session->id }}" name="status" class="form-surface mt-1 block w-full rounded-2xl border px-4 py-3 shadow-sm">
                            @foreach(['confirmed' => 'Confirmed', 'maybe' => 'Maybe', 'declined' => 'Declined'] as $value => $label)
                                <option value="{{ $value }}" @selected(($attendance?->status?->value ?? 'pending') === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label :for="'note-'.$session->id" :value="__('Note')" />
                        <textarea id="note-{{ $session->id }}" name="note" rows="2" class="form-surface mt-1 block w-full rounded-[1.5rem] border px-4 py-3 shadow-sm">{{ $attendance?->note }}</textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save RSVP') }}</x-primary-button>
                        @if($attendance)
                            <span class="text-xs" style="color: var(--app-text-muted);">{{ __('Current status: :status', ['status' => $attendance->status->value]) }}</span>
                        @endif
                    </div>
                </form>
            @endauth
        </article>
    @empty
        <div class="page-card-soft border-dashed text-sm" style="color: var(--app-text-muted);">
            {{ __('No sessions scheduled yet.') }}
        </div>
    @endforelse
</div>
