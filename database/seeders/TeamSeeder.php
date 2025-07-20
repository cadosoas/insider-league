<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public const TEAMS = [
        [
            'name' => 'Manchester City',
            'strength' => 5,
        ],
        [
            'name' => 'Arsenal',
            'strength' => 4,
        ],
        [
            'name' => 'Liverpool',
            'strength' => 4,
        ],
        [
            'name' => 'Aston Villa',
            'strength' => 3,
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (self::TEAMS as $team) {
            Team::create($team);
        }
    }
}
