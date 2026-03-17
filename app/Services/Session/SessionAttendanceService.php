<?php

namespace App\Services\Session;

use App\Enums\CampaignMemberStatus;
use App\Enums\MessageType;
use App\Models\CampaignSession;
use App\Models\Message;
use App\Models\SessionAttendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SessionAttendanceService
{
    public function respond(User $user, CampaignSession $session, array $data): SessionAttendance
    {
        return DB::transaction(function () use ($user, $session, $data): SessionAttendance {
            $membership = $user->campaignMemberships()
                ->where('campaign_id', $session->campaign_id)
                ->where('status', CampaignMemberStatus::ACTIVE)
                ->firstOrFail();

            $attendance = SessionAttendance::query()->updateOrCreate(
                [
                    'session_id' => $session->getKey(),
                    'user_id' => $membership->user_id,
                ],
                [
                    'status' => $data['status'],
                    'note' => $data['note'] ?? null,
                    'responded_at' => now(),
                ],
            );

            Message::query()->create([
                'campaign_id' => $session->campaign_id,
                'user_id' => $user->getKey(),
                'session_id' => $session->getKey(),
                'type' => MessageType::SESSION_NOTICE,
                'content' => "{$user->name} responded {$attendance->status->value} to {$session->title}",
                'metadata' => [
                    'attendance_id' => $attendance->getKey(),
                ],
            ]);

            return $attendance->refresh();
        });
    }
}
