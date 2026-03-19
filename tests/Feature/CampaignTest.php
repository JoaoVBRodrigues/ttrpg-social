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

    public function test_requesting_user_can_see_pending_status_on_campaign_page(): void
    {
        $campaign = Campaign::factory()->create([
            'visibility' => CampaignVisibility::PUBLIC,
            'status' => CampaignStatus::OPEN,
        ]);
        $user = User::factory()->create();

        $campaign->members()->create([
            'user_id' => $user->id,
            'role' => CampaignMemberRole::PLAYER,
            'status' => CampaignMemberStatus::PENDING,
        ]);

        $response = $this->actingAs($user)->get(route('campaigns.show', $campaign));

        $response
            ->assertOk()
            ->assertSee('Join request pending');
    }

    public function test_gm_can_approve_a_join_request(): void
    {
        $owner = User::factory()->create();
        $requester = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'owner_id' => $owner->id,
            'visibility' => CampaignVisibility::PUBLIC,
        ]);

        $campaign->members()->create([
            'user_id' => $owner->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $membership = $campaign->members()->create([
            'user_id' => $requester->id,
            'role' => CampaignMemberRole::PLAYER,
            'status' => CampaignMemberStatus::PENDING,
        ]);

        $response = $this->actingAs($owner)->patch(route('campaign-members.review', $membership), [
            'status' => CampaignMemberStatus::ACTIVE->value,
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('campaign_members', [
            'id' => $membership->id,
            'status' => CampaignMemberStatus::ACTIVE->value,
        ]);
    }

    public function test_gm_can_deny_a_join_request_with_a_reason(): void
    {
        $owner = User::factory()->create();
        $requester = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'owner_id' => $owner->id,
            'visibility' => CampaignVisibility::PUBLIC,
        ]);

        $campaign->members()->create([
            'user_id' => $owner->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $membership = $campaign->members()->create([
            'user_id' => $requester->id,
            'role' => CampaignMemberRole::PLAYER,
            'status' => CampaignMemberStatus::PENDING,
        ]);

        $response = $this->actingAs($owner)->patch(route('campaign-members.review', $membership), [
            'status' => CampaignMemberStatus::REJECTED->value,
            'message' => 'We already filled the final seat for this arc.',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('campaign_members', [
            'id' => $membership->id,
            'status' => CampaignMemberStatus::REJECTED->value,
            'review_message' => 'We already filled the final seat for this arc.',
        ]);

        $this->actingAs($requester)
            ->get(route('campaigns.show', $campaign))
            ->assertOk()
            ->assertSee('Join request declined')
            ->assertSee('We already filled the final seat for this arc.');
    }

    public function test_pending_join_request_panel_is_only_visible_to_campaign_managers(): void
    {
        $owner = User::factory()->create();
        $outsider = User::factory()->create();
        $requester = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'owner_id' => $owner->id,
            'visibility' => CampaignVisibility::PUBLIC,
        ]);

        $campaign->members()->create([
            'user_id' => $owner->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $campaign->members()->create([
            'user_id' => $requester->id,
            'role' => CampaignMemberRole::PLAYER,
            'status' => CampaignMemberStatus::PENDING,
        ]);

        $this->actingAs($owner)
            ->get(route('campaigns.show', $campaign))
            ->assertOk()
            ->assertSee('Pending join requests')
            ->assertSee('@'.$requester->username);

        $this->actingAs($outsider)
            ->get(route('campaigns.show', $campaign))
            ->assertOk()
            ->assertDontSee('Pending join requests')
            ->assertDontSee('@'.$requester->username);
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

    public function test_my_campaigns_page_shows_owned_and_active_member_campaigns_only(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownedCampaign = Campaign::factory()->create([
            'owner_id' => $user->id,
            'title' => 'Owned Table',
        ]);

        $ownedCampaign->members()->create([
            'user_id' => $user->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $memberCampaign = Campaign::factory()->create([
            'owner_id' => $otherUser->id,
            'title' => 'Member Table',
        ]);

        $memberCampaign->members()->create([
            'user_id' => $otherUser->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $memberCampaign->members()->create([
            'user_id' => $user->id,
            'role' => CampaignMemberRole::PLAYER,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $pendingCampaign = Campaign::factory()->create([
            'owner_id' => $otherUser->id,
            'title' => 'Pending Table',
        ]);

        $pendingCampaign->members()->create([
            'user_id' => $otherUser->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        $pendingCampaign->members()->create([
            'user_id' => $user->id,
            'role' => CampaignMemberRole::PLAYER,
            'status' => CampaignMemberStatus::PENDING,
        ]);

        $response = $this->actingAs($user)->get(route('campaigns.mine'));

        $response
            ->assertOk()
            ->assertSee('My Campaigns')
            ->assertSee('Owned Table')
            ->assertSee('Member Table')
            ->assertDontSee('Pending Table');
    }
}
