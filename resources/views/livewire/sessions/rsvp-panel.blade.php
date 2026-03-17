<div class="space-y-4">
    @forelse($sessions as $session)
        @php($attendance = $session->attendances->first())
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h4 class="text-lg font-medium text-slate-900">{{ $session->title }}</h4>
                    <p class="mt-1 text-sm text-slate-500">{{ $session->starts_at?->timezone(auth()->user()?->timezone ?? $session->timezone)->format('M d, Y H:i') }} · {{ $session->timezone }}</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">{{ ucfirst($session->status->value) }}</span>
            </div>

            @if($session->description)
                <p class="mt-4 text-sm leading-6 text-slate-600">{{ $session->description }}</p>
            @endif

            @auth
                <form method="POST" action="{{ route('campaign-sessions.attendance.update', $session) }}" class="mt-4 space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <x-input-label :for="'status-'.$session->id" :value="__('RSVP')" />
                        <select id="status-{{ $session->id }}" name="status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach(['confirmed' => 'Confirmed', 'maybe' => 'Maybe', 'declined' => 'Declined'] as $value => $label)
                                <option value="{{ $value }}" @selected(($attendance?->status?->value ?? 'pending') === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label :for="'note-'.$session->id" :value="__('Note')" />
                        <textarea id="note-{{ $session->id }}" name="note" rows="2" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $attendance?->note }}</textarea>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save RSVP') }}</x-primary-button>
                        @if($attendance)
                            <span class="text-xs text-slate-500">{{ __('Current status: :status', ['status' => $attendance->status->value]) }}</span>
                        @endif
                    </div>
                </form>
            @endauth
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-sm text-slate-500">
            {{ __('No sessions scheduled yet.') }}
        </div>
    @endforelse
</div>
