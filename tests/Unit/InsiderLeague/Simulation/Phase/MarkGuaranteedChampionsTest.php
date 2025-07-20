<?php

namespace Tests\Unit\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\Simulation\Phase\MarkGuaranteedChampions;
use App\InsiderLeague\Simulation\Simulation;
use App\Models\Table;
use App\Models\Team;
use Illuminate\Support\Arr;
use Tests\TestCase;

class MarkGuaranteedChampionsTest extends TestCase
{
    public function test_marks_a_team_as_champion_if_others_cannot_reach_them()
    {
        // create teams
        $team1 = Team::create(['name' => 'Galatasaray', 'strength' => 90]);
        $team2 = Team::create(['name' => 'Fenerbahçe', 'strength' => 75]);

        // create table
        Table::create(['team_id' => $team1->id, 'points' => 10, 'played' => 3]);
        Table::create(['team_id' => $team2->id, 'points' => 1, 'played' => 3]);

        // create simulation instance
        $simulation = (new Simulation())
            ->setTable(Table::with('team')->get())
            ->setMaxPoints(6)
            ->setPredictions([
                ['team' => $team1->toArray(), 'percentage' => 0, 'possibility' => true],
                ['team' => $team2->toArray(), 'percentage' => 0, 'possibility' => true],
            ]);

        // simulate the next phase
        $next = fn() => throw new \Exception('Next should not be called');

        // run the MarkGuaranteedChampions phase
        $result = (new MarkGuaranteedChampions())->handle($simulation, $next);

        // verify the champion is marked
        $this->assertTrue($result->getIsChampionGuaranteed());

        // verify the predictions
        $predictions = $result->getPredictions();;
        $this->assertEquals(100, Arr::get($predictions, "0.percentage"));
        $this->assertTrue(Arr::get($predictions, "0.possibility"));
        $this->assertEquals(0, Arr::get($predictions, "1.percentage"));
        $this->assertFalse(Arr::get($predictions, "1.possibility"));
    }

    public function test_calls_next_if_no_champion_is_guaranteed()
    {
        // create teams
        $team1 = Team::create(['name' => 'Galatasaray', 'strength' => 90]);
        $team2 = Team::create(['name' => 'Fenerbahçe', 'strength' => 75]);
        $team3 = Team::create(['name' => 'Beşiktaş', 'strength' => 80]);
        $team4 = Team::create(['name' => 'Trabzonspor', 'strength' => 85]);

        // create tables
        Table::create(['team_id' => $team1->id, 'points' => 10, 'played' => 3]);
        Table::create(['team_id' => $team2->id, 'points' => 9, 'played' => 3]);
        Table::create(['team_id' => $team3->id, 'points' => 8, 'played' => 3]);
        Table::create(['team_id' => $team4->id, 'points' => 7, 'played' => 3]);

        // create simulation instance
        $simulation = (new Simulation())
            ->setTable(Table::with('team')->get())
            ->setMaxPoints(6)
            ->setPredictions([
                ['team' => $team1->toArray(), 'percentage' => 0, 'possibility' => true],
                ['team' => $team2->toArray(), 'percentage' => 0, 'possibility' => true],
                ['team' => $team3->toArray(), 'percentage' => 0, 'possibility' => true],
                ['team' => $team4->toArray(), 'percentage' => 0, 'possibility' => true],
            ]);

        // simulate the next phase
        $called = false;
        $next = function ($simulation) use (&$called) {
            $called = true;
            return $simulation;
        };

        // run the MarkGuaranteedChampions phase
        $result = (new MarkGuaranteedChampions())->handle($simulation, $next);

        // verify no champion is marked
        $this->assertFalse($result->getIsChampionGuaranteed());
        $this->assertTrue($called);
    }
}
