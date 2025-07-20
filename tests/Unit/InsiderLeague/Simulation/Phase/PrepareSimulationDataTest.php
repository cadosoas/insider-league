<?php

namespace Tests\Unit\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\Simulation\Phase\PrepareSimulationData;
use App\InsiderLeague\Simulation\Simulation;
use App\Models\Table;
use App\Models\Team;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PrepareSimulationDataTest extends TestCase
{
    public function test_prepares_simulation_data_from_database_table()
    {
        // create fake team
        $team = Team::create([
            'name' => 'Galatasaray',
            'strength' => 85
        ]);

        // create fake table
        Table::create([
            'team_id' => $team->id,
            'played' => 3,
            'points' => 9,
        ]);

        // set simulation with the table
        $simulation = (new Simulation())
            ->setTable(
                Table::with('team')->get()
            );

        // run the PrepareSimulationData phase
        $result = (new PrepareSimulationData())
            ->handle($simulation, fn ($simulation) => $simulation);

        // verify simulation properties
        $this->assertEquals(3, $result->getRemainingMatches());
        $this->assertEquals(9, $result->getMaxPoints());

        $predictions = $result->getPredictions();

        // verify predictions
        $this->assertCount(1, $predictions);
        $this->assertEquals([
            'team' => [
                'id' => $team->id,
                'name' => $team->name,
                'strength' => $team->strength,
            ],
            'percentage' => 0,
            'possibility' => true,
        ], Arr::first($predictions));
    }
}
