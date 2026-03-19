<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="border-b border-white/10 bg-white/50 backdrop-blur dark:border-white/5 dark:bg-slate-950/50">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-20 justify-between">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-4" wire:navigate>
                        <x-application-logo class="block h-10 w-auto fill-current text-[color:var(--app-accent)]" />
                        <div class="hidden sm:block">
                            <p class="font-display text-lg font-semibold" style="color: var(--app-text);">{{ config('app.name', 'TTRPG Social') }}</p>
                            <p class="text-xs uppercase tracking-[0.2em]" style="color: var(--app-text-muted);">{{ __('Play together, stay organized') }}</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('campaigns.index')" :active="request()->routeIs('campaigns.index') || request()->routeIs('campaigns.show')" wire:navigate>
                        {{ __('Campaigns') }}
                    </x-nav-link>
                    @auth
                        <x-nav-link :href="route('campaigns.mine')" :active="request()->routeIs('campaigns.mine')" wire:navigate>
                            {{ __('My Campaigns') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:gap-3 sm:ms-6">
                <x-locale-switcher />
                <x-theme-toggle />

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center rounded-full border border-white/10 bg-white/50 px-4 py-2 text-sm font-medium shadow-sm transition hover:border-amber-300/40 dark:bg-slate-900/60">
                                <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile')" wire:navigate>
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex items-center gap-4">
                        <a href="{{ route('login') }}" class="text-sm font-medium theme-link transition" wire:navigate>
                            {{ __('Log in') }}
                        </a>
                        <a href="{{ route('register') }}" class="accent-button inline-flex items-center rounded-full px-4 py-2 text-sm font-semibold transition" wire:navigate>
                            {{ __('Register') }}
                        </a>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 transition duration-150 ease-in-out hover:bg-white/10 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="space-y-1 px-4 pb-3 pt-2">
            <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/40 px-4 py-3 dark:bg-slate-900/60">
                <x-locale-switcher />
                <x-theme-toggle />
            </div>

            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-white/10 pb-1 pt-4 dark:border-white/5">
            @auth
                <div class="px-4">
                    <div class="text-base font-medium" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                    <div class="text-sm font-medium" style="color: var(--app-text-muted);">{{ auth()->user()->email }}</div>
                </div>
            @endauth

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('campaigns.index')" :active="request()->routeIs('campaigns.index') || request()->routeIs('campaigns.show')" wire:navigate>
                    {{ __('Campaigns') }}
                </x-responsive-nav-link>

                @auth
                    <x-responsive-nav-link :href="route('campaigns.mine')" :active="request()->routeIs('campaigns.mine')" wire:navigate>
                        {{ __('My Campaigns') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('profile')" wire:navigate>
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <button wire:click="logout" class="w-full text-start">
                        <x-responsive-nav-link>
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </button>
                @else
                    <x-responsive-nav-link :href="route('login')" wire:navigate>
                        {{ __('Log in') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')" wire:navigate>
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                @endauth
            </div>
        </div>
    </div>
</nav>
