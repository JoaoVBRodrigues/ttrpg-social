<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_profile_page_is_visible_when_profile_is_public(): void
    {
        $user = User::factory()->create([
            'username' => 'gmprofile',
            'bio' => 'Long-time GM looking for cinematic campaigns.',
            'favorite_systems' => ['D&D 5e', 'Daggerheart'],
            'availability' => [['day' => 'Friday', 'window' => '20:00-23:00']],
            'is_profile_public' => true,
        ]);

        $response = $this->get(route('profile.public', $user));

        $response
            ->assertOk()
            ->assertSee('gmprofile')
            ->assertSee('Long-time GM')
            ->assertSee('Daggerheart');
    }

    public function test_private_profile_page_is_hidden_from_other_users(): void
    {
        $user = User::factory()->create([
            'username' => 'privategm',
            'is_profile_public' => false,
        ]);

        $response = $this->get(route('profile.public', $user));

        $response->assertNotFound();
    }
}
