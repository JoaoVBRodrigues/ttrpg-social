<?php

namespace App\Services\Message;

use App\Enums\CampaignMemberStatus;
use App\Enums\MessageType;
use App\Events\CampaignMessageCreated;
use App\Exceptions\DomainException;
use App\Models\Campaign;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public function postTextMessage(User $user, Campaign $campaign, array $data): Message
    {
        $this->ensureActiveMember($user, $campaign);

        return DB::transaction(function () use ($user, $campaign, $data): Message {
            $message = Message::query()->create([
                'campaign_id' => $campaign->getKey(),
                'user_id' => $user->getKey(),
                'session_id' => $data['session_id'] ?? null,
                'type' => MessageType::TEXT,
                'content' => $data['content'],
                'metadata' => [],
            ]);

            CampaignMessageCreated::dispatch($message);

            return $message->load('user');
        });
    }

    public function recentMessages(Campaign $campaign, int $perPage = 30)
    {
        return Message::query()
            ->with('user')
            ->where('campaign_id', $campaign->getKey())
            ->latest()
            ->paginate($perPage);
    }

    public function ensureActiveMember(User $user, Campaign $campaign): void
    {
        $isActive = $user->campaignMemberships()
            ->where('campaign_id', $campaign->getKey())
            ->where('status', CampaignMemberStatus::ACTIVE)
            ->exists();

        if (! $isActive) {
            throw new DomainException('Only active members can interact with the campaign chat.', 403);
        }
    }
}
