<?php

namespace Database\Factories;

use App\Enums\CampaignSessionStatus;
use App\Models\Campaign;
use App\Models\CampaignSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CampaignSession>
 */
class CampaignSessionFactory extends Factory
{
    protected $model = CampaignSession::class;

    public function definition(): array
    {
        $startsAt = now()->addDays(fake()->numberBetween(1, 14));

        return [
            'campaign_id' => Campaign::factory(),
            'created_by' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->addHours(4),
            'timezone' => 'UTC',
            'status' => CampaignSessionStatus::SCHEDULED,
            'cancellation_reason' => null,
        ];
    }
}
