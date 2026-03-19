<div class="space-y-4" x-data x-on:campaign-chat-message-posted.window="$nextTick(() => $refs.chatList.scrollTop = 0)">
    @if(auth()->check() && auth()->user()->campaignMemberships()->where('campaign_id', $campaign->id)->where('status', 'active')->exists())
        <form wire:submit="sendMessage" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <h4 class="text-base font-medium text-slate-900">{{ __('Post a message') }}</h4>
                <span class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ __('Realtime') }}</span>
            </div>

            <div class="mt-4">
                <x-input-label for="chat_message_content" :value="__('Message')" />
                <textarea
                    id="chat_message_content"
                    wire:model="content"
                    rows="4"
                    class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required
                ></textarea>
                <x-input-error class="mt-2" :messages="$errors->get('content')" />
            </div>

            <label for="chat_message_is_important" class="mt-4 flex items-start gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                <input id="chat_message_is_important" wire:model="isImportant" type="checkbox" class="mt-1 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span>{{ __('Mark as important to notify members using their message notification preferences.') }}</span>
            </label>

            <div class="mt-4 flex justify-end">
                <x-primary-button wire:loading.attr="disabled" wire:target="sendMessage">
                    {{ __('Send message') }}
                </x-primary-button>
            </div>
        </form>
    @endif

    <div x-ref="chatList" class="max-h-[34rem] space-y-3 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @forelse($messages as $message)
            <article wire:key="campaign-message-{{ $message->id }}" class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-900">{{ $message->user->name }}</p>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $message->type->value }}</p>
                    </div>
                    <span class="text-xs text-slate-500">{{ $message->created_at?->timezone(auth()->user()?->timezone ?? $campaign->timezone)->format('M d H:i') }}</span>
                </div>

                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $message->content }}</p>
            </article>
        @empty
            <p class="text-sm text-slate-500">{{ __('No messages yet.') }}</p>
        @endforelse
    </div>

    <div>
        {{ $messages->links() }}
    </div>
</div>
