<?php

namespace App\Services\Session;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Enums\CampaignSessionStatus;
use App\Enums\MessageType;
use App\Models\Campaign;
use App\Models\CampaignSession;
use App\Models\Message;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CampaignSessionService
{
    public function createSession(User $actor, Campaign $campaign, array $data): CampaignSession
    {
        return DB::transaction(function () use ($actor, $campaign, $data): CampaignSession {
            $session = CampaignSession::query()->create([
                ...Arr::only($data, ['title', 'description', 'timezone']),
                'campaign_id' => $campaign->getKey(),
                'created_by' => $actor->getKey(),
                'starts_at' => CarbonImmutable::parse($data['starts_at'], $data['timezone'])->utc(),
                'ends_at' => CarbonImmutable::parse($data['ends_at'], $data['timezone'])->utc(),
                'status' => $data['status'] ?? CampaignSessionStatus::SCHEDULED->value,
            ]);

            $campaign->members()
                ->where('status', CampaignMemberStatus::ACTIVE)
                ->get()
                ->each(function ($member) use ($session): void {
                    $session->attendances()->create([
                        'user_id' => $member->user_id,
                        'status' => 'pending',
                    ]);
                });

            $this->refreshNextSessionAt($campaign);
            $this->createSystemNotice($campaign, $actor, "Session scheduled: {$session->title}");

            return $session->load('attendances');
        });
    }

    public function updateSession(User $actor, CampaignSession $session, array $data): CampaignSession
    {
        return DB::transaction(function () use ($actor, $session, $data): CampaignSession {
            $session->fill([
                ...Arr::only($data, ['title', 'description', 'timezone', 'status', 'cancellation_reason']),
                'starts_at' => CarbonImmutable::parse($data['starts_at'], $data['timezone'])->utc(),
                'ends_at' => CarbonImmutable::parse($data['ends_at'], $data['timezone'])->utc(),
            ]);
            $session->save();

            $this->refreshNextSessionAt($session->campaign);
            $this->createSystemNotice($session->campaign, $actor, "Session updated: {$session->title}");

            return $session->refresh()->load('attendances');
        });
    }

    public function canManage(User $user, Campaign $campaign): bool
    {
        $membership = $user->campaignMemberships()->where('campaign_id', $campaign->getKey())->first();

        return $membership !== null
            && $membership->status === CampaignMemberStatus::ACTIVE
            && in_array($membership->role, [CampaignMemberRole::GM, CampaignMemberRole::CO_GM], true);
    }

    protected function refreshNextSessionAt(Campaign $campaign): void
    {
        $nextSessionAt = $campaign->sessions()
            ->where('status', CampaignSessionStatus::SCHEDULED->value)
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->value('starts_at');

        $campaign->forceFill([
            'next_session_at' => $nextSessionAt,
        ])->save();
    }

    protected function createSystemNotice(Campaign $campaign, User $actor, string $content): void
    {
        Message::query()->create([
            'campaign_id' => $campaign->getKey(),
            'user_id' => $actor->getKey(),
            'type' => MessageType::SESSION_NOTICE,
            'content' => $content,
            'metadata' => [],
        ]);
    }
}
