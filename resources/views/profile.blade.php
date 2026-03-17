<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Profile') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Manage your public identity, TTRPG preferences, and notification settings.') }}
                </p>
            </div>
            <a
                href="{{ route('profile.public', $user) }}"
                class="inline-flex items-center rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
            >
                {{ __('View public profile') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'profile-updated')
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ __('Profile updated successfully.') }}
                </div>
            @endif

            @if (session('status') === 'notification-preferences-updated')
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ __('Notification preferences updated successfully.') }}
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="POST" action="{{ route('profile.update') }}" class="grid gap-6 lg:grid-cols-2">
                    @csrf
                    @method('PATCH')

                    <div class="lg:col-span-2">
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Profile information') }}</h3>
                        <p class="mt-1 text-sm text-slate-500">
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
                        <select id="preferred_role" name="preferred_role" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                        <p class="mt-1 text-xs text-slate-500">{{ __('Comma-separated list, for example: D&D 5e, Pathfinder 2e.') }}</p>
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="bio" :value="__('Bio')" />
                        <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $user->bio) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                    </div>

                    <div class="lg:col-span-2">
                        <x-input-label for="availability_text" :value="__('Availability')" />
                        <textarea id="availability_text" name="availability_text" rows="4" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('availability_text', collect($user->availability ?? [])->map(fn ($entry) => ($entry['day'] ?? '').': '.($entry['window'] ?? ''))->implode(PHP_EOL)) }}</textarea>
                        <p class="mt-1 text-xs text-slate-500">{{ __('One line per slot using the format Day: time window.') }}</p>
                        <x-input-error class="mt-2" :messages="$errors->get('availability')" />
                    </div>

                    <div class="flex items-start gap-3">
                        <input id="is_profile_public" name="is_profile_public" type="checkbox" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_profile_public', $user->is_profile_public))>
                        <label for="is_profile_public" class="text-sm text-slate-600">
                            <span class="block font-medium text-slate-900">{{ __('Public profile') }}</span>
                            {{ __('Allow other players to view your profile page.') }}
                        </label>
                    </div>

                    <div class="flex items-start gap-3">
                        <input id="is_email_public" name="is_email_public" type="checkbox" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_email_public', $user->is_email_public))>
                        <label for="is_email_public" class="text-sm text-slate-600">
                            <span class="block font-medium text-slate-900">{{ __('Show email publicly') }}</span>
                            {{ __('Only enable this if you want your email visible on your public profile.') }}
                        </label>
                    </div>

                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                        <div class="lg:col-span-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                            {{ __('Your email address is still unverified.') }}
                        </div>
                    @endif

                    <div class="lg:col-span-2 flex items-center gap-4">
                        <x-primary-button>{{ __('Save profile') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="POST" action="{{ route('profile.preferences.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <h3 class="text-lg font-medium text-slate-900">{{ __('Notification preferences') }}</h3>
                        <p class="mt-1 text-sm text-slate-500">
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
                            <label class="flex items-start gap-3 rounded-lg border border-slate-200 p-4">
                                <input type="checkbox" name="{{ $field }}" value="1" class="mt-1 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old($field, $notificationPreferences->{$field}))>
                                <span class="text-sm text-slate-700">{{ __($label) }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Save preferences') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.update-password-form />
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <livewire:profile.delete-user-form />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
