<?php

namespace Tests\Feature;

use App\Enums\CampaignStatus;
use App\Enums\CampaignVisibility;
use App\Models\Campaign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_ttrpg_product_content(): void
    {
        Campaign::factory()->create([
            'title' => 'Echoes Below Brightwater',
            'visibility' => CampaignVisibility::PUBLIC,
            'status' => CampaignStatus::OPEN,
        ]);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('The home for campaigns, players, and every moment between sessions.')
            ->assertSee('Browse public campaigns')
            ->assertSee('Echoes Below Brightwater');
    }

    public function test_locale_switcher_can_persist_portuguese_in_session(): void
    {
        $response = $this->from(route('campaigns.index'))
            ->get(route('locale.update', 'pt_BR'));

        $response
            ->assertRedirect(route('campaigns.index'))
            ->assertSessionHas('locale', 'pt_BR');

        $this->withSession(['locale' => 'pt_BR'])
            ->get(route('campaigns.index'))
            ->assertOk()
            ->assertSee('Campanhas');
    }
}
