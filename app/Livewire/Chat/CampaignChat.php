<?php

namespace App\Livewire\Chat;

use App\Exceptions\DomainException;
use App\Models\Campaign;
use App\Services\Message\MessageService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CampaignChat extends Component
{
    public Campaign $campaign;

    public string $content = '';

    public bool $isImportant = false;

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    protected function getListeners(): array
    {
        return [
            "echo-private:campaign.{$this->campaign->id},CampaignMessageCreated" => 'refreshChat',
        ];
    }

    public function refreshChat(): void
    {
        //
    }

    public function sendMessage(MessageService $service): void
    {
        $validated = $this->validate([
            'content' => ['required', 'string', 'max:5000'],
            'isImportant' => ['boolean'],
        ]);

        abort_unless(auth()->check(), 403);

        try {
            $service->postTextMessage(auth()->user(), $this->campaign, [
                'content' => $validated['content'],
                'is_important' => $validated['isImportant'],
            ]);
        } catch (DomainException $exception) {
            $this->addError('content', $exception->getMessage());

            return;
        }

        $this->reset('content', 'isImportant');
        $this->dispatch('campaign-chat-message-posted');
    }

    public function render(MessageService $service): View
    {
        return view('livewire.chat.campaign-chat', [
            'messages' => $service->recentMessages($this->campaign, 30),
            'campaign' => $this->campaign,
        ]);
    }
}
