@php($editing = isset($campaign))

<form method="POST" action="{{ $editing ? route('campaigns.update', $campaign) : route('campaigns.store') }}" class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <x-input-label for="title" :value="__('Title')" />
            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $campaign->title ?? '')" required />
            <x-input-error class="mt-2" :messages="$errors->get('title')" />
        </div>

        <div>
            <x-input-label for="game_system_id" :value="__('Game system')" />
            <select id="game_system_id" name="game_system_id" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach($gameSystems as $system)
                    <option value="{{ $system->id }}" @selected((string) old('game_system_id', $campaign->game_system_id ?? '') === (string) $system->id)>{{ $system->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('game_system_id')" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="synopsis" :value="__('Synopsis')" />
            <textarea id="synopsis" name="synopsis" rows="3" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('synopsis', $campaign->synopsis ?? '') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('synopsis')" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="description" :value="__('Description')" />
            <textarea id="description" name="description" rows="5" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $campaign->description ?? '') }}</textarea>
        </div>

        <div class="md:col-span-2">
            <x-input-label for="rules_summary" :value="__('House rules / summary')" />
            <textarea id="rules_summary" name="rules_summary" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('rules_summary', $campaign->rules_summary ?? '') }}</textarea>
        </div>

        <div>
            <x-input-label for="max_players" :value="__('Max players')" />
            <x-text-input id="max_players" name="max_players" type="number" min="1" max="12" class="mt-1 block w-full" :value="old('max_players', $campaign->max_players ?? 5)" required />
        </div>

        <div>
            <x-input-label for="visibility" :value="__('Visibility')" />
            <select id="visibility" name="visibility" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                @foreach(['public' => 'Public', 'unlisted' => 'Unlisted', 'private' => 'Private'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('visibility', $campaign->visibility->value ?? 'public') === $value)>{{ __($label) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach(['draft' => 'Draft', 'open' => 'Open', 'full' => 'Full', 'ongoing' => 'Ongoing', 'paused' => 'Paused', 'finished' => 'Finished', 'archived' => 'Archived'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $campaign->status->value ?? 'open') === $value)>{{ __($label) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <x-input-label for="language" :value="__('Language')" />
            <x-text-input id="language" name="language" type="text" class="mt-1 block w-full" :value="old('language', $campaign->language ?? 'en')" required />
        </div>

        <div>
            <x-input-label for="timezone" :value="__('Timezone')" />
            <x-text-input id="timezone" name="timezone" type="text" class="mt-1 block w-full" :value="old('timezone', $campaign->timezone ?? auth()->user()?->timezone ?? 'UTC')" required />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="frequency_label" :value="__('Frequency')" />
            <x-text-input id="frequency_label" name="frequency_label" type="text" class="mt-1 block w-full" :value="old('frequency_label', $campaign->frequency_label ?? '')" />
        </div>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ $editing ? __('Save campaign') : __('Create campaign') }}</x-primary-button>
    </div>
</form>
