<?php

namespace App\Events;

use App\Http\Resources\Message\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignMessageCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('campaign.'.$this->message->campaign_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'CampaignMessageCreated';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => (new MessageResource($this->message->load('user')))->resolve(),
        ];
    }
}
