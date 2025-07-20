<?php

namespace App\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\Simulation\Simulation;

class PrepareSimulationData
{
    public function handle(Simulation $simulation, \Closure $next)
    {
        // get table and first table
        $table = $simulation->getTable();
        $first = $table->first();

        // get remaining matches and max points
        $remainingMatches = Simulation::LEAGUE_FINISH_WEEK - $first->played;
        $maxPoints = $remainingMatches * 3;

        // Set the simulation properties
        $simulation->setMaxPoints($maxPoints);
        $simulation->setRemainingMatches($remainingMatches);

        // //////////////////////
        // Initialize predictions
        // //////////////////////

        $predictions = [];
        foreach ($table as $team) {
            $predictions[] = [
                'team' => $team->team->toArray(),
                'percentage' => 0,
                'possibility' => true,
            ];
        }

        $simulation->setPredictions($predictions);

        return $next($simulation);
    }
}
