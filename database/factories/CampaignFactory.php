<?php

namespace Database\Factories;

use App\Enums\CampaignStatus;
use App\Enums\CampaignVisibility;
use App\Models\Campaign;
use App\Models\GameSystem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'owner_id' => User::factory(),
            'game_system_id' => GameSystem::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1000, 9999),
            'synopsis' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'rules_summary' => fake()->sentence(),
            'max_players' => fake()->numberBetween(3, 6),
            'visibility' => CampaignVisibility::PUBLIC,
            'status' => CampaignStatus::OPEN,
            'language' => 'en',
            'timezone' => 'UTC',
            'frequency_label' => 'Weekly',
            'next_session_at' => now()->addWeek(),
        ];
    }
}
