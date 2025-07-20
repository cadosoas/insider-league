<?php

namespace App\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\Simulation\Simulation;

class MarkGuaranteedChampions
{
    public function handle(Simulation $simulation, \Closure $next)
    {
        $table = $simulation->getTable();
        $maxPoints = $simulation->getMaxPoints();

        foreach ($table as $team) {
            if ($this->isChampionGuaranteed($team, $table, $maxPoints)) {

                $predictions = $simulation->getPredictions();

                foreach ($predictions as &$prediction) {
                    if ($prediction['team']['id'] === $team->team->id) {
                        $prediction['percentage'] = 100;
                        $prediction['possibility'] = true;
                    } else {
                        $prediction['percentage'] = 0;
                        $prediction['possibility'] = false;
                    }
                }

                $simulation->setPredictions($predictions);
                $simulation->setIsChampionGuaranteed(true);

                return $simulation; // simulation finished early if a champion is guaranteed
            }
        }

        return $next($simulation);
    }

    /**
     * if a team is guaranteed to be champion
     *
     * @param $team
     * @param $table
     * @param $maxPoints
     * @return bool
     */
    public function isChampionGuaranteed($team, $table, $maxPoints): bool
    {
        $teamPoints = $team->points;

        foreach ($table as $other) {
            if ($other->id === $team->id) {
                continue;
            }

            $maxPossible = $other->points + $maxPoints;

            if ($maxPossible >= $teamPoints) {
                return false;
            }
        }

        return true;
    }
}
