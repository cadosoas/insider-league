<?php

namespace Tests\Unit\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\LeagueRepository;
use App\InsiderLeague\Simulation\Phase\SimulateChampionship;
use App\InsiderLeague\Simulation\Simulation;
use App\Models\Fixture;
use App\Models\Table;
use App\Models\Team;
use Illuminate\Support\Arr;
use Tests\TestCase;

class SimulateChampionshipTest extends TestCase
{
    public function test_simulates_championship_and_updates_predictions()
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

        // create fixtures
        $fixtures = app(LeagueRepository::class);
        $fixtures->generateFixtures();

        $simulation = (new Simulation())
            ->setTable(Table::with('team')->get())
            ->setCurrentWeek(4)
            ->setPredictions([
                ['team' => $team1->toArray(), 'possibility' => true, 'percentage' => 0],
                ['team' => $team2->toArray(), 'possibility' => true, 'percentage' => 0],
                ['team' => $team3->toArray(), 'possibility' => true, 'percentage' => 0],
                ['team' => $team4->toArray(), 'possibility' => true, 'percentage' => 0],
            ]);

        // run the simulation phases
        $result = (new SimulateChampionship())->handle($simulation, fn($simulation) => $simulation);

        // verify the predictions
        $predictions = $result->getPredictions();
        $this->assertCount(4, $predictions);
        $this->assertGreaterThanOrEqual(0, Arr::get($predictions, "0.percentage"));
        $this->assertLessThanOrEqual(100, Arr::get($predictions, "0.percentage"));
    }

    public function test_calculate_champions_percentage_is_correct()
    {
        $simulate = app(SimulateChampionship::class);

        $result = $simulate
            ->calculateChampionsPercentage([
                1 => 80,
                2 => 20,
            ]);

        $this->assertEquals(80.0, $result[1]);
        $this->assertEquals(20.0, $result[2]);
    }

    public function test_calculates_form_based_on_last_three_matches()
    {
        $home = Team::create(['name' => 'Galatasaray', 'strength' => 90]);
        $away = Team::create(['name' => 'Fenerbahçe', 'strength' => 75]);

        // create fixtures with scores
        Fixture::create([
            'week' => 1,
            'home_id' => $home->id,
            'away_id' => $away->id,
            'home_score' => 2,
            'away_score' => 1,
            'played_at' => now(),
        ]);

        Fixture::create([
            'week' => 2,
            'home_id' => $away->id,
            'away_id' => $home->id,
            'home_score' => 1,
            'away_score' => 1,
            'played_at' => now(),
        ]);

        Fixture::create([
            'week' => 3,
            'home_id' => $away->id,
            'away_id' => $home->id,
            'home_score' => 2,
            'away_score' => 0,
            'played_at' => now(),
        ]);

        // verify team form
        $form = app(SimulateChampionship::class)->getTeamForm($home);

        $this->assertEquals(1.33, $form);
    }

    public function test_calculate_fixture_returns_valid_points()
    {
        // create teams
        $home = Team::create(['name' => 'Galatasaray', 'strength' => 90]);
        $away = Team::create(['name' => 'Fenerbahçe', 'strength' => 75]);

        // create fixture
        $fixture = new Fixture();
        $fixture->setRelation('home', $home);
        $fixture->setRelation('away', $away);

        $simulator = new SimulateChampionship();

        [$homePoints, $awayPoints] = $simulator->calculateFixture($fixture);

        $this->assertContains($homePoints, [0, 1, 3]);
        $this->assertContains($awayPoints, [0, 1, 3]);
        $this->assertContains($homePoints + $awayPoints, [0, 2, 3]);
    }
}
