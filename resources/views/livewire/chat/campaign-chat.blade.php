<div class="space-y-4" x-data x-on:campaign-chat-message-posted.window="$nextTick(() => $refs.chatList.scrollTop = 0)">
    @if(auth()->check() && auth()->user()->campaignMemberships()->where('campaign_id', $campaign->id)->where('status', 'active')->exists())
        <form wire:submit="sendMessage" class="page-card-soft !p-5">
            <div class="flex items-center justify-between gap-4">
                <h4 class="font-display text-2xl">{{ __('Post a message') }}</h4>
                <span class="page-chip">{{ __('Realtime') }}</span>
            </div>

            <div class="mt-4">
                <x-input-label for="chat_message_content" :value="__('Message')" />
                <textarea
                    id="chat_message_content"
                    wire:model="content"
                    rows="4"
                    class="form-surface mt-1 block w-full rounded-[1.5rem] border px-4 py-3 shadow-sm focus:border-amber-400/40 focus:ring-amber-400/30"
                    required
                ></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('content')" />
            </div>

            <label for="chat_message_is_important" class="page-card-soft mt-4 flex items-start gap-3 !p-4 text-sm leading-6" style="color: var(--app-text-muted);">
                <input id="chat_message_is_important" wire:model="isImportant" type="checkbox" class="checkbox-accent mt-1 rounded shadow-sm">
                <span>{{ __('Mark as important to notify members using their message notification preferences.') }}</span>
            </label>

            <div class="mt-4 flex justify-end">
                <x-primary-button wire:loading.attr="disabled" wire:target="sendMessage">
                    {{ __('Send message') }}
                </x-primary-button>
            </div>
        </form>
    @endif

    <div x-ref="chatList" class="page-card max-h-[34rem] space-y-3 overflow-y-auto !p-5">
        @forelse($messages as $message)
            <article wire:key="campaign-message-{{ $message->id }}" class="page-card-soft !p-4">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium" style="color: var(--app-text);">{{ $message->user->name }}</p>
                        <p class="text-xs uppercase tracking-[0.2em]" style="color: var(--app-text-muted);">{{ $message->type->value }}</p>
                    </div>
                    <span class="text-xs" style="color: var(--app-text-muted);">{{ $message->created_at?->timezone(auth()->user()?->timezone ?? $campaign->timezone)->format('M d H:i') }}</span>
                </div>

                <p class="mt-3 whitespace-pre-line text-sm leading-6" style="color: var(--app-text);">{{ $message->content }}</p>
            </article>
        @empty
            <p class="text-sm" style="color: var(--app-text-muted);">{{ __('No messages yet.') }}</p>
        @endforelse
    </div>

    <div>
        {{ $messages->links() }}
    </div>
</div>
