<?php

namespace App\Livewire\Chat;

use App\Models\Campaign;
use App\Services\Message\MessageService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CampaignChat extends Component
{
    public Campaign $campaign;

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

    public function render(MessageService $service): View
    {
        return view('livewire.chat.campaign-chat', [
            'messages' => $service->recentMessages($this->campaign, 30),
            'campaign' => $this->campaign,
        ]);
    }
}
