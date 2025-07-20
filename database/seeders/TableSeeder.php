<?php

namespace Database\Seeders;

use App\Models\Table;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach (Team::all() as $team) {
            Table::create([
                'team_id' => $team->id,
            ]);
        }
    }
}
