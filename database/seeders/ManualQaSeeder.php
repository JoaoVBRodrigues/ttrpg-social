<?php

namespace Database\Seeders;

use App\Enums\CampaignMemberRole;
use App\Enums\CampaignMemberStatus;
use App\Enums\CampaignSessionStatus;
use App\Enums\CampaignStatus;
use App\Enums\CampaignVisibility;
use App\Models\Campaign;
use App\Models\CampaignReference;
use App\Models\CampaignSession;
use App\Models\GameSystem;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManualQaSeeder extends Seeder
{
    public const GM_EMAIL = 'gm.qa@example.com';

    public const PLAYER_EMAIL = 'player.qa@example.com';

    public const PASSWORD = 'password';

    public const CAMPAIGN_SLUG = 'echoes-below-brightwater';

    public function run(): void
    {
        $system = GameSystem::query()->where('slug', 'dnd-5e')->firstOrFail();

        $gm = User::query()->updateOrCreate(
            ['email' => self::GM_EMAIL],
            [
                'name' => 'Mara Vale',
                'username' => 'maravale_gm',
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
                'timezone' => 'America/Sao_Paulo',
                'preferred_role' => 'gm',
                'bio' => 'Narrator focused on character drama, exploration, and collaborative storytelling.',
                'favorite_systems' => ['Dungeons & Dragons 5e', 'Daggerheart', 'Call of Cthulhu'],
                'availability' => [
                    ['day' => 'Wednesday', 'window' => '19:30-22:30'],
                    ['day' => 'Saturday', 'window' => '14:00-18:00'],
                ],
                'is_profile_public' => true,
                'is_email_public' => false,
            ],
        );

        $player = User::query()->updateOrCreate(
            ['email' => self::PLAYER_EMAIL],
            [
                'name' => 'Leo Martins',
                'username' => 'leo_player',
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
                'timezone' => 'America/Sao_Paulo',
                'preferred_role' => 'player',
                'bio' => 'Player who enjoys mystery arcs, downtime roleplay, and tactical encounters.',
                'favorite_systems' => ['Dungeons & Dragons 5e', 'Pathfinder 2e'],
                'availability' => [
                    ['day' => 'Wednesday', 'window' => '19:30-22:30'],
                ],
                'is_profile_public' => true,
                'is_email_public' => false,
            ],
        );

        NotificationPreference::query()->updateOrCreate(
            ['user_id' => $gm->getKey()],
            [
                'email_sessions_enabled' => true,
                'email_invites_enabled' => true,
                'email_messages_enabled' => true,
                'in_app_sessions_enabled' => true,
                'in_app_invites_enabled' => true,
                'in_app_messages_enabled' => true,
            ],
        );

        NotificationPreference::query()->updateOrCreate(
            ['user_id' => $player->getKey()],
            [
                'email_sessions_enabled' => true,
                'email_invites_enabled' => true,
                'email_messages_enabled' => false,
                'in_app_sessions_enabled' => true,
                'in_app_invites_enabled' => true,
                'in_app_messages_enabled' => true,
            ],
        );

        $campaign = Campaign::query()->updateOrCreate(
            ['slug' => self::CAMPAIGN_SLUG],
            [
                'owner_id' => $gm->getKey(),
                'game_system_id' => $system->getKey(),
                'title' => 'Echoes Below Brightwater',
                'synopsis' => 'A public, roleplay-forward fantasy campaign about a haunted port city and the secrets below its tide tunnels.',
                'description' => 'Brightwater is thriving on the surface, but smugglers, restless spirits, and drowned ruins beneath the city are pulling the district toward disaster. The table balances investigation, tense social scenes, and occasional tactical combat.',
                'rules_summary' => 'Session zero safety tools are mandatory. Collaborative spotlight, respectful play, and punctual RSVP updates are expected from everyone.',
                'max_players' => 5,
                'visibility' => CampaignVisibility::PUBLIC->value,
                'status' => CampaignStatus::OPEN->value,
                'language' => 'en',
                'timezone' => 'America/Sao_Paulo',
                'frequency_label' => 'Weekly on Wednesdays',
                'next_session_at' => now()->addDays(7)->setTime(19, 30),
            ],
        );

        $campaign->members()->updateOrCreate(
            ['user_id' => $gm->getKey()],
            [
                'role' => CampaignMemberRole::GM,
                'status' => CampaignMemberStatus::ACTIVE,
                'joined_at' => now(),
                'invited_by' => null,
            ],
        );

        $campaign->members()
            ->where('user_id', $player->getKey())
            ->delete();

        CampaignReference::query()->updateOrCreate(
            [
                'campaign_id' => $campaign->getKey(),
                'title' => 'Player Primer',
            ],
            [
                'created_by' => $gm->getKey(),
                'type' => 'intro_material',
                'content' => 'New characters should have a reason to protect Brightwater, a rumor about the old tide tunnels, and at least one connection in the harbor district.',
                'external_url' => null,
                'sort_order' => 1,
            ],
        );

        CampaignSession::query()->updateOrCreate(
            [
                'campaign_id' => $campaign->getKey(),
                'title' => 'Session Zero Recap',
            ],
            [
                'description' => 'Safety tools, party hooks, and the first map of the tide tunnels.',
                'starts_at' => now()->addDays(7)->setTime(19, 30)->utc(),
                'ends_at' => now()->addDays(7)->setTime(22, 30)->utc(),
                'timezone' => 'America/Sao_Paulo',
                'status' => CampaignSessionStatus::SCHEDULED,
                'created_by' => $gm->getKey(),
            ],
        );
    }
}
