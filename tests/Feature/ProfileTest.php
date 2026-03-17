<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response
            ->assertOk()
            ->assertSee('Profile information')
            ->assertSee('Notification preferences')
            ->assertSeeVolt('profile.update-password-form')
            ->assertSeeVolt('profile.delete-user-form');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'bio' => 'Forever looking for weekly tables.',
            'timezone' => 'America/Sao_Paulo',
            'preferred_role' => 'both',
            'favorite_systems' => 'D&D 5e, Pathfinder 2e',
            'availability_text' => "Wednesday: 19:00-22:00\nSaturday: 14:00-18:00",
            'is_profile_public' => '1',
            'is_email_public' => '0',
        ]);

        $response->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('testuser', $user->username);
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('America/Sao_Paulo', $user->timezone);
        $this->assertSame(['D&D 5e', 'Pathfinder 2e'], $user->favorite_systems);
        $this->assertCount(2, $user->availability);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Test User',
            'username' => $user->username,
            'email' => $user->email,
            'bio' => $user->bio,
            'timezone' => $user->timezone,
            'preferred_role' => $user->preferred_role,
            'favorite_systems' => '',
            'availability_text' => '',
            'is_profile_public' => '1',
            'is_email_public' => '0',
        ]);

        $response->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_notification_preferences_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/profile/preferences', [
            'email_sessions_enabled' => '1',
            'email_invites_enabled' => '0',
            'email_messages_enabled' => '1',
            'in_app_sessions_enabled' => '1',
            'in_app_invites_enabled' => '1',
            'in_app_messages_enabled' => '0',
        ]);

        $response->assertRedirect('/profile');

        $preference = $user->refresh()->notificationPreference;

        $this->assertNotNull($preference);
        $this->assertTrue($preference->email_sessions_enabled);
        $this->assertFalse($preference->email_invites_enabled);
        $this->assertTrue($preference->email_messages_enabled);
        $this->assertFalse($preference->in_app_messages_enabled);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser');

        $component
            ->assertHasErrors('password')
            ->assertNoRedirect();

        $this->assertNotNull($user->fresh());
    }
}
