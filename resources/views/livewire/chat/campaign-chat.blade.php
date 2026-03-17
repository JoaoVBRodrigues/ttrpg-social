<div class="space-y-4">
    <div class="max-h-[34rem] space-y-3 overflow-y-auto rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @forelse($messages as $message)
            <article class="rounded-2xl border border-slate-100 bg-slate-50 px-4 py-3">
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
