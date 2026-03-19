<?php

namespace Tests\Feature;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Events\CampaignMessageCreated;
use App\Livewire\Chat\CampaignChat;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class CampaignChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_member_can_post_a_message(): void
    {
        Event::fake([CampaignMessageCreated::class]);

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

        $response = $this->actingAs($player)->post(route('campaigns.messages.store', $campaign), [
            'content' => 'Ready for tonight?',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('messages', [
            'campaign_id' => $campaign->id,
            'user_id' => $player->id,
            'type' => 'text',
            'content' => 'Ready for tonight?',
        ]);

        Event::assertDispatched(CampaignMessageCreated::class);
    }

    public function test_active_member_can_roll_dice_and_persist_result(): void
    {
        Event::fake([CampaignMessageCreated::class]);

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

        $response = $this->actingAs($player)->post(route('campaigns.rolls.store', $campaign), [
            'expression' => '1d20+4',
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('dice_rolls', [
            'campaign_id' => $campaign->id,
            'user_id' => $player->id,
            'expression' => '1d20+4',
        ]);

        $this->assertDatabaseHas('messages', [
            'campaign_id' => $campaign->id,
            'user_id' => $player->id,
            'type' => 'dice_roll',
        ]);

        Event::assertDispatched(CampaignMessageCreated::class);
    }

    public function test_non_member_cannot_post_in_campaign_chat(): void
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('campaigns.messages.store', $campaign), [
            'content' => 'Let me in.',
        ]);

        $response->assertForbidden();
    }

    public function test_livewire_chat_can_post_without_full_page_redirect_flow(): void
    {
        Event::fake([CampaignMessageCreated::class]);

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

        $this->actingAs($player);

        Livewire::test(CampaignChat::class, ['campaign' => $campaign])
            ->set('content', 'Livewire keeps this smooth.')
            ->set('isImportant', true)
            ->call('sendMessage')
            ->assertSet('content', '')
            ->assertSet('isImportant', false)
            ->assertSee('Livewire keeps this smooth.');

        $this->assertDatabaseHas('messages', [
            'campaign_id' => $campaign->id,
            'user_id' => $player->id,
            'content' => 'Livewire keeps this smooth.',
            'is_important' => 1,
        ]);

        Event::assertDispatched(CampaignMessageCreated::class);
    }
}
