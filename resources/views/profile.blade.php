<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-500">{{ __('Account and identity') }}</p>
                <h2 class="mt-3 font-display text-3xl leading-tight">
                    {{ __('Profile') }}
                </h2>
                <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">
                    {{ __('Manage your public identity, TTRPG preferences, and notification settings.') }}
                </p>
            </div>
            @if (filled($user->username))
                <a
                    href="{{ route('profile.public', ['user' => $user]) }}"
                    class="page-outline-button"
                >
                    {{ __('View public profile') }}
                </a>
            @else
                <p class="max-w-xs text-sm leading-6" style="color: var(--app-text-muted);">
                    {{ __('Choose a username below to enable your public profile link.') }}
                </p>
            @endif
        </div>
    </x-slot>

    <div class="page-shell">
        <div class="page-stack mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('status') === 'profile-updated')
                <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                    {{ __('Profile updated successfully.') }}
                </div>
            @endif

            @if (session('status') === 'notification-preferences-updated')
                <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300">
                    {{ __('Notification preferences updated successfully.') }}
                </div>
            @endif

            <div class="page-card">
                <form method="POST" action="{{ route('profile.update') }}" class="grid gap-6 lg:grid-cols-2">
                    @csrf
                    @method('PATCH')

                    <div class="lg:col-span-2">
                        <h3 class="font-display text-2xl">{{ __('Profile information') }}</h3>
                        <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">
                            {{ __('Shape how other players discover you and how the platform stores your preferences.') }}
                        </p>
                    </div>

                    <div>
                        <x-input-label for="name" :value="__('Display name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="username" :value="__('Username')" />
                        <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('username')" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="timezone" :value="__('Timezone')" />
                        <x-text-input id="timezone" name="timezone" type="text" class="mt-1 block w-full" :value="old('timezone', $user->timezone)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('timezone')" />
                    </div>

                    <div>
                        <x-input-label for="preferred_role" :value="__('Preferred role')" />
                        <select id="preferred_role" name="preferred_role" class="form-surface mt-1 block w-full rounded-2xl border px-4 py-3 shadow-sm focus:border-amber-400/40 focus:ring-amber-400/30">
                            @foreach (['player' => 'Player', 'gm' => 'Game Master', 'both' => 'Both'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('preferred_role', $user->preferred_role) === $value)>{{ __($label) }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('preferred_role')" />
                    </div>

                    <div>
                        <x-input-label for="favorite_systems" :value="__('Favorite systems')" />
                        <x-text-input
                            id="favorite_systems"
                            name="favorite_systems"
                            type="text"
                            class="mt-1 block w-full"
                            :value="old('favorite_systems', implode(', ', $user->favorite_systems ?? []))"
                        />
                        <p class="mt-2 text-xs leading-6" style="color: var(--app-text-muted);">{{ __('Comma-separated list, for example: D&D 5e, Pathfinder 2e.') }}</p>
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="bio" :value="__('Bio')" />
                        <textarea id="bio" name="bio" rows="4" class="form-surface mt-1 block w-full rounded-[1.5rem] border px-4 py-3 shadow-sm focus:border-amber-400/40 focus:ring-amber-400/30">{{ old('bio', $user->bio) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="availability_text" :value="__('Availability')" />
                        <textarea id="availability_text" name="availability_text" rows="4" class="form-surface mt-1 block w-full rounded-[1.5rem] border px-4 py-3 shadow-sm focus:border-amber-400/40 focus:ring-amber-400/30">{{ old('availability_text', collect($user->availability ?? [])->map(fn ($entry) => ($entry['day'] ?? '').': '.($entry['window'] ?? ''))->implode(PHP_EOL)) }}</textarea>
                        <p class="mt-2 text-xs leading-6" style="color: var(--app-text-muted);">{{ __('One line per slot using the format Day: time window.') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('availability')" />
                    </div>

                    <div class="page-card-soft !p-5">
                        <div class="flex items-start gap-3">
                            <input id="is_profile_public" name="is_profile_public" type="checkbox" value="1" class="checkbox-accent mt-1 rounded shadow-sm" @checked(old('is_profile_public', $user->is_profile_public))>
                            <label for="is_profile_public" class="text-sm leading-6" style="color: var(--app-text-muted);">
                                <span class="block font-medium" style="color: var(--app-text);">{{ __('Public profile') }}</span>
                                {{ __('Allow other players to view your profile page.') }}
                            </label>
                        </div>
                    </div>

                    <div class="page-card-soft !p-5">
                        <div class="flex items-start gap-3">
                            <input id="is_email_public" name="is_email_public" type="checkbox" value="1" class="checkbox-accent mt-1 rounded shadow-sm" @checked(old('is_email_public', $user->is_email_public))>
                            <label for="is_email_public" class="text-sm leading-6" style="color: var(--app-text-muted);">
                                <span class="block font-medium" style="color: var(--app-text);">{{ __('Show email publicly') }}</span>
                                {{ __('Only enable this if you want your email visible on your public profile.') }}
                            </label>
                        </div>
                    </div>

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <div class="lg:col-span-2 rounded-2xl border border-amber-300/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-700 dark:text-amber-300">
                            {{ __('Your email address is still unverified.') }}
                        </div>
                    @endif

                    <div class="lg:col-span-2 flex items-center gap-4">
                        <x-primary-button>{{ __('Save profile') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="page-card">
                <form method="POST" action="{{ route('profile.preferences.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <h3 class="font-display text-2xl">{{ __('Notification preferences') }}</h3>
                        <p class="mt-2 text-sm leading-7" style="color: var(--app-text-muted);">
                            {{ __('Choose which updates should reach you by email or stay only inside the platform.') }}
                        </p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ([
                            'email_sessions_enabled' => 'Email me about session scheduling',
                            'email_invites_enabled' => 'Email me about campaign invites',
                            'email_messages_enabled' => 'Email me about important message activity',
                            'in_app_sessions_enabled' => 'In-app notifications for sessions',
                            'in_app_invites_enabled' => 'In-app notifications for invites',
                            'in_app_messages_enabled' => 'In-app notifications for message activity',
                        ] as $field => $label)
                            <label class="page-card-soft !p-4 flex items-start gap-3">
                                <input type="checkbox" name="{{ $field }}" value="1" class="checkbox-accent mt-1 rounded shadow-sm" @checked(old($field, $notificationPreferences->{$field}))>
                                <span class="text-sm leading-6" style="color: var(--app-text);">{{ __($label) }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save preferences') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="page-card">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="page-card">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
