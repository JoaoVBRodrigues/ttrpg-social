<?php

namespace Database\Seeders;

use App\Models\GameSystem;
use Illuminate\Database\Seeder;

class GameSystemSeeder extends Seeder
{
    public function run(): void
    {
        $systems = [
            ['name' => 'Dungeons & Dragons 5e', 'slug' => 'dnd-5e', 'is_official' => true],
            ['name' => 'Pathfinder 2e', 'slug' => 'pathfinder-2e', 'is_official' => true],
            ['name' => 'Tormenta 20', 'slug' => 'tormenta-20', 'is_official' => true],
            ['name' => 'Ordem Paranormal RPG', 'slug' => 'ordem-paranormal-rpg', 'is_official' => true],
            ['name' => 'Daggerheart', 'slug' => 'daggerheart', 'is_official' => true],
            ['name' => 'Call of Cthulhu', 'slug' => 'call-of-cthulhu', 'is_official' => true],
            ['name' => 'Vampire: The Masquerade', 'slug' => 'vampire-the-masquerade', 'is_official' => true],
            ['name' => 'Homebrew', 'slug' => 'homebrew', 'is_official' => false],
        ];

        foreach ($systems as $system) {
            GameSystem::query()->updateOrCreate(
                ['slug' => $system['slug']],
                [
                    'name' => $system['name'],
                    'description' => null,
                    'is_official' => $system['is_official'],
                    'metadata' => [],
                ],
            );
        }
    }
}
