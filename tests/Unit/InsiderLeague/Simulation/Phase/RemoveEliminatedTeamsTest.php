<?php

namespace Tests\Unit\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\Simulation\Phase\RemoveEliminatedTeams;
use App\InsiderLeague\Simulation\Simulation;
use App\Models\Table;
use App\Models\Team;
use Illuminate\Support\Arr;
use Tests\TestCase;

class RemoveEliminatedTeamsTest extends TestCase
{
    public function test_sets_possibility_false_for_eliminated_teams()
    {
        // create teams
        $team1 = Team::create(['name' => 'Galatasaray', 'strength' => 90]);
        $team2 = Team::create(['name' => 'Fenerbahçe', 'strength' => 75]);

        // create table
        Table::create(['team_id' => $team1->id, 'points' => 9, 'played' => 3]);
        Table::create(['team_id' => $team2->id, 'points' => 1, 'played' => 3]);

        // create simulation instance
        $simulation = (new Simulation())
            ->setTable(Table::with('team')->get())
            ->setMaxPoints(6)
            ->setPredictions([
                ['team' => $team1->toArray(), 'possibility' => true, 'percentage' => 0],
                ['team' => $team2->toArray(), 'possibility' => true, 'percentage' => 0],
            ]);

        // run the RemoveEliminatedTeams phase
        $result = (new RemoveEliminatedTeams())->handle($simulation, fn ($simulation) => $simulation);

        // verify that the predictions were updated
        $predictions = $result->getPredictions();

        $this->assertTrue(Arr::get($predictions, "0.possibility"));
        $this->assertFalse(Arr::get($predictions, "1.possibility"));
        $this->assertEquals(0, Arr::get($predictions, "1.percentage"));
    }
}
