<?php

namespace Tests\Feature;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Jobs\DispatchSessionNotificationJob;
use App\Jobs\SendCampaignInviteNotificationJob;
use App\Jobs\SendCampaignMembershipReviewedNotificationJob;
use App\Jobs\SendSessionReminderJob;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CampaignNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_inviting_a_member_dispatches_the_invite_notification_job(): void
    {
        Queue::fake();

        $owner = User::factory()->create();
        $invitee = User::factory()->create(['username' => 'queued-player']);
        $campaign = Campaign::factory()->create(['owner_id' => $owner->id]);

        $campaign->members()->create([
            'user_id' => $owner->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($owner)->post(route('campaigns.members.invite', $campaign), [
            'username' => $invitee->username,
            'role' => CampaignMemberRole::PLAYER->value,
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $membershipId = $campaign->members()->where('user_id', $invitee->id)->value('id');

        Queue::assertPushed(SendCampaignInviteNotificationJob::class, function (SendCampaignInviteNotificationJob $job) use ($membershipId): bool {
            return $job->membershipId === $membershipId;
        });
    }

    public function test_scheduling_a_session_dispatches_session_notification_and_reminder_jobs(): void
    {
        Queue::fake();

        [$owner, $campaign] = $this->createManagedCampaign();

        $startsAt = now()->addDays(3)->setTime(19, 0);
        $endsAt = $startsAt->copy()->addHours(3);

        $response = $this->actingAs($owner)->post(route('campaign-sessions.store', $campaign), [
            'title' => 'Castle Siege',
            'description' => 'The party breaches the outer wall.',
            'starts_at' => $startsAt->format('Y-m-d H:i:s'),
            'ends_at' => $endsAt->format('Y-m-d H:i:s'),
            'timezone' => 'UTC',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $sessionId = $campaign->sessions()->value('id');

        Queue::assertPushed(DispatchSessionNotificationJob::class, function (DispatchSessionNotificationJob $job) use ($sessionId, $owner): bool {
            return $job->sessionId === $sessionId
                && $job->kind === 'scheduled'
                && $job->actorId === $owner->id;
        });

        Queue::assertPushed(SendSessionReminderJob::class, 2);
    }

    public function test_updating_a_session_dispatches_updated_notification_job(): void
    {
        Queue::fake();

        [$owner, $campaign] = $this->createManagedCampaign();

        $session = $campaign->sessions()->create([
            'created_by' => $owner->id,
            'title' => 'Session One',
            'description' => 'Original briefing.',
            'starts_at' => now()->addDays(4),
            'ends_at' => now()->addDays(4)->addHours(2),
            'timezone' => 'UTC',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($owner)->put(route('campaign-sessions.update', $session), [
            'title' => 'Session One - Rescheduled',
            'description' => 'Updated briefing.',
            'starts_at' => now()->addDays(5)->setTime(20, 0)->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDays(5)->setTime(23, 0)->format('Y-m-d H:i:s'),
            'timezone' => 'UTC',
            'status' => 'scheduled',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        Queue::assertPushed(DispatchSessionNotificationJob::class, function (DispatchSessionNotificationJob $job) use ($session, $owner): bool {
            return $job->sessionId === $session->id
                && $job->kind === 'updated'
                && $job->actorId === $owner->id;
        });

        Queue::assertPushed(SendSessionReminderJob::class, 2);
    }

    public function test_reviewing_a_membership_dispatches_the_review_notification_job(): void
    {
        Queue::fake();

        [$owner, $campaign] = $this->createManagedCampaign();
        $applicant = User::factory()->create();

        $membership = $campaign->members()->create([
            'user_id' => $applicant->id,
            'role' => CampaignMemberRole::PLAYER,
            'status' => CampaignMemberStatus::PENDING,
        ]);

        $response = $this->actingAs($owner)->patch(route('campaign-members.review', $membership), [
            'status' => CampaignMemberStatus::ACTIVE->value,
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        Queue::assertPushed(SendCampaignMembershipReviewedNotificationJob::class, function (SendCampaignMembershipReviewedNotificationJob $job) use ($membership): bool {
            return $job->membershipId === $membership->id;
        });
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\Campaign}
     */
    protected function createManagedCampaign(): array
    {
        $owner = User::factory()->create();
        $player = User::factory()->create();
        $campaign = Campaign::factory()->create(['owner_id' => $owner->id, 'timezone' => 'UTC']);

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
        ]);

        return [$owner, $campaign];
    }
}
