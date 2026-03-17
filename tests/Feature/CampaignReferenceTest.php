<?php

namespace Tests\Feature;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Models\Campaign;
use App\Models\CampaignReference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignReferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_create_a_compendium_entry(): void
    {
        [$owner, $campaign] = $this->createManagedCampaign();

        $response = $this->actingAs($owner)->post(route('campaigns.references.store', $campaign), [
            'title' => 'Safety Tools',
            'type' => 'house_rule',
            'content' => 'Lines and veils are reviewed before session zero.',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('campaigns.show', $campaign));

        $this->assertDatabaseHas('campaign_references', [
            'campaign_id' => $campaign->id,
            'created_by' => $owner->id,
            'title' => 'Safety Tools',
            'type' => 'house_rule',
        ]);
    }

    public function test_manager_can_update_and_delete_a_compendium_entry(): void
    {
        [$owner, $campaign] = $this->createManagedCampaign();

        $reference = CampaignReference::query()->create([
            'campaign_id' => $campaign->id,
            'created_by' => $owner->id,
            'title' => 'New Player Guide',
            'type' => 'intro_material',
            'content' => 'Bring a level 1 character concept.',
            'sort_order' => 1,
        ]);

        $updateResponse = $this->actingAs($owner)->putJson(route('campaign-references.update', $reference), [
            'title' => 'Updated Player Guide',
            'type' => 'intro_material',
            'content' => 'Bring a level 3 character concept.',
            'external_url' => 'https://example.com/player-guide',
            'sort_order' => 2,
        ]);

        $updateResponse
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Player Guide')
            ->assertJsonPath('data.external_url', 'https://example.com/player-guide')
            ->assertJsonPath('data.sort_order', 2);

        $deleteResponse = $this->actingAs($owner)->deleteJson(route('campaign-references.destroy', $reference));

        $deleteResponse->assertNoContent();

        $this->assertDatabaseMissing('campaign_references', [
            'id' => $reference->id,
        ]);
    }

    public function test_non_manager_cannot_manage_campaign_references(): void
    {
        [$owner, $campaign] = $this->createManagedCampaign();
        $outsider = User::factory()->create();

        $reference = CampaignReference::query()->create([
            'campaign_id' => $campaign->id,
            'created_by' => $owner->id,
            'title' => 'Lore Primer',
            'type' => 'system_note',
            'content' => 'The empire is split into seven provinces.',
            'sort_order' => 1,
        ]);

        $storeResponse = $this->actingAs($outsider)->post(route('campaigns.references.store', $campaign), [
            'title' => 'Unauthorized',
            'type' => 'system_note',
            'content' => 'Should not be allowed.',
        ]);

        $storeResponse->assertForbidden();

        $updateResponse = $this->actingAs($outsider)->put(route('campaign-references.update', $reference), [
            'title' => 'Still Unauthorized',
            'type' => 'system_note',
            'content' => 'Should not be allowed.',
        ]);

        $updateResponse->assertForbidden();
    }

    /**
     * @return array{0: \App\Models\User, 1: \App\Models\Campaign}
     */
    protected function createManagedCampaign(): array
    {
        $owner = User::factory()->create();
        $campaign = Campaign::factory()->create(['owner_id' => $owner->id]);

        $campaign->members()->create([
            'user_id' => $owner->id,
            'role' => CampaignMemberRole::GM,
            'status' => CampaignMemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

        return [$owner, $campaign];
    }
}
