<?php

namespace Tests\Feature;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Enums\SessionAttendanceStatus;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_gm_can_schedule_a_session_and_seed_attendances(): void
    {
        $owner = User::factory()->create();
        $player = User::factory()->create();
        $campaign = Campaign::factory()->create(['owner_id' => $owner->id, 'timezone' => 'America/Sao_Paulo']);

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

        $response = $this->actingAs($owner)->post(route('campaign-sessions.store', $campaign), [
            'title' => 'Session Zero',
            'description' => 'Character setup and expectations.',
            'starts_at' => '2026-04-01 19:00:00',
            'ends_at' => '2026-04-01 22:00:00',
            'timezone' => 'America/Sao_Paulo',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $session = $campaign->sessions()->firstOrFail();

        $this->assertSame('2026-04-01 22:00:00', $session->starts_at->format('Y-m-d H:i:s'));
        $this->assertDatabaseCount('session_attendances', 2);
        $this->assertDatabaseHas('messages', [
            'campaign_id' => $campaign->id,
            'session_id' => null,
            'type' => 'session_notice',
        ]);
    }

    public function test_active_member_can_respond_to_rsvp(): void
    {
        $owner = User::factory()->create();
        $player = User::factory()->create();
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
        ]);

        $session = $campaign->sessions()->create([
            'created_by' => $owner->id,
            'title' => 'Session One',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHours(4),
            'timezone' => 'UTC',
            'status' => 'scheduled',
        ]);

        $session->attendances()->create([
            'user_id' => $player->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($player)->put(route('campaign-sessions.attendance.update', $session), [
            'status' => 'confirmed',
            'note' => 'Ready to go.',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('session_attendances', [
            'session_id' => $session->id,
            'user_id' => $player->id,
            'status' => SessionAttendanceStatus::CONFIRMED->value,
            'note' => 'Ready to go.',
        ]);
    }

    public function test_non_manager_cannot_schedule_a_session(): void
    {
        $owner = User::factory()->create();
        $player = User::factory()->create();
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
        ]);

        $response = $this->actingAs($player)->post(route('campaign-sessions.store', $campaign), [
            'title' => 'Not Allowed',
            'starts_at' => '2026-04-01 19:00:00',
            'ends_at' => '2026-04-01 22:00:00',
            'timezone' => 'UTC',
        ]);

        $response->assertForbidden();
    }
}
