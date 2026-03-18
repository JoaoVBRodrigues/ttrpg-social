<?php

namespace Tests\Feature;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Enums\CampaignStatus;
use App\Enums\CampaignVisibility;
use App\Models\Campaign;
use App\Models\GameSystem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_user_can_view_the_create_campaign_page(): void
    {
        $user = User::factory()->create();
        GameSystem::factory()->create(['slug' => 'dnd-5e', 'name' => 'D&D 5e']);

        $response = $this->actingAs($user)->get(route('campaigns.create'));

        $response
            ->assertOk()
            ->assertSee('Create campaign')
            ->assertSee('D&D 5e');
    }

    public function test_verified_user_can_create_a_campaign(): void
    {
        $user = User::factory()->create();
        $system = GameSystem::factory()->create(['slug' => 'dnd-5e', 'name' => 'D&D 5e']);

        $response = $this->actingAs($user)->post('/campaigns', [
            'game_system_id' => $system->id,
            'title' => 'Wednesday Night Heroes',
            'synopsis' => 'Weekly heroic fantasy with strong social play.',
            'description' => 'A long-running campaign focused on political intrigue.',
            'rules_summary' => 'Respect everyone at the table.',
            'max_players' => 5,
            'visibility' => CampaignVisibility::PUBLIC->value,
            'status' => CampaignStatus::OPEN->value,
            'language' => 'en',
            'timezone' => 'America/Sao_Paulo',
            'frequency_label' => 'Weekly',
        ]);

        $campaign = Campaign::query()->firstOrFail();

        $response->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('campaigns', [
            'title' => 'Wednesday Night Heroes',
            'owner_id' => $user->id,
        ]);

        $this->assertDatabaseHas('campaign_members', [
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'role' => CampaignMemberRole::GM->value,
            'status' => CampaignMemberStatus::ACTIVE->value,
        ]);
    }

    public function test_campaign_index_can_be_filtered_as_json(): void
    {
        $systemA = GameSystem::factory()->create(['slug' => 'dnd-5e', 'name' => 'D&D 5e']);
        $systemB = GameSystem::factory()->create(['slug' => 'pathfinder-2e', 'name' => 'Pathfinder 2e']);

        Campaign::factory()->create([
            'game_system_id' => $systemA->id,
            'title' => 'Open DND Table',
            'status' => CampaignStatus::OPEN,
            'visibility' => CampaignVisibility::PUBLIC,
        ]);

        Campaign::factory()->create([
            'game_system_id' => $systemB->id,
            'title' => 'Closed Pathfinder Table',
            'status' => CampaignStatus::FULL,
            'visibility' => CampaignVisibility::PUBLIC,
        ]);

        $response = $this->getJson('/campaigns?system=dnd-5e&status=open');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Open DND Table');
    }

    public function test_campaign_index_page_shows_a_view_campaign_button(): void
    {
        $campaign = Campaign::factory()->create([
            'title' => 'Open DND Table',
            'visibility' => CampaignVisibility::PUBLIC,
            'status' => CampaignStatus::OPEN,
        ]);

        $response = $this->get('/campaigns');

        $response
            ->assertOk()
            ->assertSee('View campaign')
            ->assertSee(route('campaigns.show', $campaign), false);
    }

    public function test_user_can_request_to_join_a_public_campaign(): void
    {
        $campaign = Campaign::factory()->create([
            'visibility' => CampaignVisibility::PUBLIC,
            'status' => CampaignStatus::OPEN,
        ]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('campaigns.members.request', $campaign));

        $response->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('campaign_members', [
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'status' => CampaignMemberStatus::PENDING->value,
        ]);
    }

    public function test_gm_can_invite_and_approve_a_member(): void
    {
        $owner = User::factory()->create();
        $invitee = User::factory()->create(['username' => 'newplayer']);
        $campaign = Campaign::factory()->create(['owner_id' => $owner->id]);

        $campaign->members()->create([
            'user_id' => $owner->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $inviteResponse = $this->actingAs($owner)->post(route('campaigns.members.invite', $campaign), [
            'username' => 'newplayer',
            'role' => CampaignMemberRole::PLAYER->value,
        ]);

        $inviteResponse->assertRedirect(route('campaigns.show', $campaign));

        $membership = $campaign->members()->where('user_id', $invitee->id)->firstOrFail();

        $this->assertSame(CampaignMemberStatus::INVITED, $membership->status);

        $approveResponse = $this->actingAs($owner)->patch(route('campaign-members.review', $membership), [
            'status' => CampaignMemberStatus::ACTIVE->value,
        ]);

        $approveResponse->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('campaign_members', [
            'id' => $membership->id,
            'status' => CampaignMemberStatus::ACTIVE->value,
        ]);
    }

    public function test_non_manager_cannot_edit_campaign(): void
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('campaigns.edit', $campaign));

        $response->assertForbidden();
    }
}
