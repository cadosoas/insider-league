<?php

namespace App\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\Simulation\Simulation;

class RemoveEliminatedTeams
{
    public function handle(Simulation $simulation, \Closure $next)
    {
        $table = $simulation->getTable();
        $maxPoints = $simulation->getMaxPoints();

        $firstTable = $table->first();
        $firstPoint = $firstTable->points;

        // find teams that cannot be champion
        // if their max possible points are less than the first team's points

        $isCannotBeChampions = [];
        foreach ($table as $team) {
            $maxPossible = $team->points + $maxPoints;
            if ($maxPossible < $firstPoint) {
                $isCannotBeChampions[] = $team;
            }
        }

        // if there are teams that cannot be champion, update their predictions
        // and set their possibility to false
        if (! empty($isCannotBeChampions)) {

            foreach ($isCannotBeChampions as $team) {
                $predictions = $simulation->getPredictions();

                foreach ($predictions as &$prediction) {
                    if ($prediction['team']['id'] === $team->team->id) {
                        $prediction['possibility'] = false;
                        $prediction['percentage'] = 0;
                    }
                }

                // update the simulation with the modified predictions
                $simulation->setPredictions($predictions);
            }
        }

        return $next($simulation);
    }
}
