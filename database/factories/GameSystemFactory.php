<?php

namespace Database\Factories;

use App\Models\GameSystem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<GameSystem>
 */
class GameSystemFactory extends Factory
{
    protected $model = GameSystem::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'is_official' => fake()->boolean(70),
            'metadata' => [],
        ];
    }
}
