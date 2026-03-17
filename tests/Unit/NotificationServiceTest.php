<?php

namespace Tests\Unit;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Models\Campaign;
use App\Models\CampaignMember;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Notifications\CampaignInviteNotification;
use App\Notifications\CampaignSessionScheduledNotification;
use App\Services\Notification\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_respects_disabled_invite_channels(): void
    {
        Notification::fake();

        $service = new NotificationService();
        $owner = User::factory()->create();
        $invitee = User::factory()->create();
        $campaign = Campaign::factory()->create(['owner_id' => $owner->id]);

        NotificationPreference::query()->create([
            'user_id' => $invitee->id,
            'email_sessions_enabled' => false,
            'email_invites_enabled' => false,
            'email_messages_enabled' => false,
            'in_app_sessions_enabled' => false,
            'in_app_invites_enabled' => false,
            'in_app_messages_enabled' => false,
        ]);

        $membership = CampaignMember::query()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $invitee->id,
            'role' => CampaignMemberRole::PLAYER,
            'status' => CampaignMemberStatus::INVITED,
            'invited_by' => $owner->id,
        ]);

        $service->sendCampaignInvite($membership);

        Notification::assertNothingSent();
    }

    #[Test]
    public function it_notifies_active_members_except_the_actor_for_session_schedule_updates(): void
    {
        Notification::fake();

        $service = new NotificationService();
        $owner = User::factory()->create();
        $player = User::factory()->create();
        $mutedPlayer = User::factory()->create();
        $campaign = Campaign::factory()->create(['owner_id' => $owner->id]);

        $campaign->members()->createMany([
            [
                'user_id' => $owner->id,
                'role' => CampaignMemberRole::GM,
                'status' => CampaignMemberStatus::ACTIVE,
                'joined_at' => now(),
            ],
            [
                'user_id' => $player->id,
                'role' => CampaignMemberRole::PLAYER,
                'status' => CampaignMemberStatus::ACTIVE,
                'joined_at' => now(),
            ],
            [
                'user_id' => $mutedPlayer->id,
                'role' => CampaignMemberRole::PLAYER,
                'status' => CampaignMemberStatus::ACTIVE,
                'joined_at' => now(),
            ],
        ]);

        NotificationPreference::query()->create([
            'user_id' => $player->id,
            'email_sessions_enabled' => true,
            'email_invites_enabled' => true,
            'email_messages_enabled' => false,
            'in_app_sessions_enabled' => true,
            'in_app_invites_enabled' => true,
            'in_app_messages_enabled' => true,
        ]);

        NotificationPreference::query()->create([
            'user_id' => $mutedPlayer->id,
            'email_sessions_enabled' => false,
            'email_invites_enabled' => true,
            'email_messages_enabled' => false,
            'in_app_sessions_enabled' => false,
            'in_app_invites_enabled' => true,
            'in_app_messages_enabled' => true,
        ]);

        $session = $campaign->sessions()->create([
            'created_by' => $owner->id,
            'title' => 'Faction Summit',
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHours(3),
            'timezone' => 'UTC',
            'status' => 'scheduled',
        ]);

        $service->sendSessionScheduled($session, $owner->id);

        Notification::assertSentTo($player, CampaignSessionScheduledNotification::class);
        Notification::assertNotSentTo($owner, CampaignSessionScheduledNotification::class);
        Notification::assertNotSentTo($mutedPlayer, CampaignSessionScheduledNotification::class);
    }
}
