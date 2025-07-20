<?php

namespace App\InsiderLeague\Simulation\Phase;

use App\InsiderLeague\Simulation\Simulation;
use App\Models\Fixture;

class SimulateChampionship
{
    public const SIMULATION_COUNT = 100;

    public function handle(Simulation $simulation, \Closure $next)
    {
        // get unplayed fixtures starting from the current week
        $fixtures = Fixture::unplayed()
            ->where('week', '>=', $simulation->getCurrentWeek())
            ->get()
            ->groupBy('week');

        // need fake table for simulation
        $fakeTable = $simulation
            ->getTable()
            ->mapWithKeys(fn ($item) => [$item->team->id => $item->points])
            ->toArray();

        // default champions array
        $champions = collect($simulation->getTable())
            ->mapWithKeys(fn ($item) => [$item->team->id => 0])
            ->toArray();

        // run simulations
        for ($i = 0; $i < self::SIMULATION_COUNT; $i++) {

            $simulationTable = $fakeTable;

            // simulate each week
            foreach ($fixtures as $weekFixtures) {

                // simulate each fixture in the week
                foreach ($weekFixtures as $fixture) {

                    // calculate points for home and away teams
                    [$homePoints, $awayPoints] = $this->calculateFixture($fixture);

                    // update the simulation table with the points
                    $simulationTable[$fixture->home_id] += $homePoints;
                    $simulationTable[$fixture->away_id] += $awayPoints;
                }
            }

            // get the champion team ID from the simulation table
            $championTeamId = $this->getChampionTeamId($simulationTable);

            // increment the champion count for the team
            $champions[$championTeamId]++;
        }

        // calculate the percentage of each team being champion
        $champions = $this->calculateChampionsPercentage($champions);

        // predictions update
        $simulation = $this->updatePredictions($simulation, $champions);

        return $next($simulation);
    }

    public function updatePredictions($simulation, $champions)
    {
        $predictions = $simulation->getPredictions();

        foreach ($predictions as &$prediction) {
            $teamId = $prediction['team']['id'];
            if (isset($champions[$teamId])) {
                $prediction['percentage'] = $champions[$teamId];
            } else {
                $prediction['percentage'] = 0;
            }
        }

        $predictions = collect($predictions)
            ->sortByDesc('percentage')
            ->values()
            ->toArray();

        $simulation->setPredictions($predictions);

        return $simulation;
    }

    public function calculateFixture($fixture)
    {
        $homeAdvantage = 0.1; // home advantage factor

        // get the form of the teams
        $homeForm = $this->getTeamForm($fixture->home);
        $awayForm = $this->getTeamForm($fixture->away);

        // get the strength of the teams
        $homeStrength = $fixture->home->strength;
        $awayStrength = $fixture->away->strength;

        // ////////////////////////////
        // calculate the scores based on the strength, form, and home advantage
        // ////////////////////////////

        $homeScore = ($homeStrength + $homeForm) / 2 + $homeAdvantage;
        $awayScore = ($awayStrength + $awayForm) / 2;

        $total = $homeScore + $awayScore;

        $pHomeWin = $homeScore / $total;
        $pAwayWin = $awayScore / $total;
        $pDraw = 1 - ($pHomeWin + $pAwayWin);

        $pHomeWin *= 0.75;
        $pAwayWin *= 0.75;
        $pDraw = 1 - ($pHomeWin + $pAwayWin);

        $rand = mt_rand() / mt_getrandmax();
        $homePoints = 0;
        $awayPoints = 0;

        if ($rand < $pHomeWin) {
            $homePoints = 3;
        } elseif ($rand < $pHomeWin + $pDraw) {
            $homePoints = 1;
            $awayPoints = 1;
        } else {
            $awayPoints = 3;
        }

        return [$homePoints, $awayPoints];
    }

    public function getChampionTeamId($simulationTable)
    {
        return collect($simulationTable)
            ->sortDesc()
            ->keys()
            ->first();
    }

    public function calculateChampionsPercentage($champions): array
    {
        $totalSimulations = array_sum($champions);

        return collect($champions)
            ->map(fn ($count) => round(($count / $totalSimulations) * 100, 2))
            ->sortDesc()
            ->toArray();
    }

    public function getTeamForm($team)
    {
        $matches = Fixture::played()
            ->where(function ($query) use ($team) {
                $query->where('home_id', $team->id)
                    ->orWhere('away_id', $team->id);
            })
            ->orderByDesc('week')
            ->take(3)
            ->get();

        if ($matches->isEmpty()) {
            return 0;
        }

        // calculate the form based on the last 3 matches

        $form = [];

        foreach ($matches as $match) {
            if ($match->home_id == $team->id) {
                $form[] = $match->home_score > $match->away_score ? 3
                    : ($match->home_score == $match->away_score ? 1 : 0);
            } else {
                $form[] = $match->away_score > $match->home_score ? 3
                    : ($match->away_score == $match->home_score ? 1 : 0);
            }
        }

        $totalPoints = array_sum($form);
        $averagePoints = $totalPoints / $matches->count();

        return round($averagePoints, 2);
    }
}
