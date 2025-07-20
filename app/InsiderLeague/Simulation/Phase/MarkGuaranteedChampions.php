<?php

namespace App\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\Simulation\Simulation;
use App\Models\Table;
use Illuminate\Support\Collection;

class MarkGuaranteedChampions
{
    public function handle(Simulation $simulation, \Closure $next)
    {
        $table = $simulation->getTable();
        $maxPoints = $simulation->getMaxPoints();

        foreach ($table as $row) {
            if ($this->isChampionGuaranteed($row, $table, $maxPoints)) {

                $predictions = $simulation->getPredictions();

                foreach ($predictions as &$prediction) {
                    if ($prediction['team']['id'] === $row->team->id) {
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
     * Check if a team is guaranteed to be champion
     *
     * @param Table $row
     * @param Collection<int, Table> $table
     * @param integer $maxPoints
     * @return boolean
     */
    public function isChampionGuaranteed(Table $row, Collection $table, int $maxPoints): bool
    {
        $teamPoints = $row->points;

        foreach ($table as $other) {
            if ($other->id === $row->id) {
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
