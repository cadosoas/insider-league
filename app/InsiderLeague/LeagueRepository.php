<?php

namespace App\InsiderLeague;

use App\Models\Fixture;
use App\Models\Table;
use App\Models\Team;
use Illuminate\Support\Collection;

class LeagueRepository
{
    /**
     * Get a collection of fixtures with their associated teams.
     *
     * @return Collection
     */
    public function getRankedTable(): Collection
    {
        return Table::with('team')->ranked()->get();
    }

    /**
     * Generate fixtures for the league.
     *
     * @return Collection
     */
    public function generateFixtures(): Collection
    {
        // get all teams
        $teams = Team::get();

        $teamsKeyById = $teams->keyBy('id');
        $teamIds = $teams->pluck('id')->toArray();

        ///////////////////////////////////
        // generate group fixtures
        ///////////////////////////////////

        $fixed = $teamIds[0];
        $rotating = array_slice($teamIds, 1);

        $schedule = [];

        for ($round = 0; $round < 3; $round++) {
            $week = [];

            $teamA = $fixed;
            $teamB = $rotating[$round % 3];
            $week[] = ($round % 2 === 0) ? [$teamA, $teamB] : [$teamB, $teamA];

            $t1 = $rotating[($round + 1) % 3];
            $t2 = $rotating[($round + 2) % 3];
            $week[] = ($round % 2 === 0) ? [$t1, $t2] : [$t2, $t1];

            $schedule[] = $week;
        }

        $secondHalf = array_map(fn ($week) => array_map(fn ($m) => [$m[1], $m[0]], $week), $schedule);
        $fullSchedule = array_merge($schedule, $secondHalf);

        ///////////////////////////////////
        // create fixtures
        ///////////////////////////////////

        $weekNum = 1;
        $fixtures = collect();

        foreach ($fullSchedule as $week) {
            foreach ($week as [$home, $away]) {

                // create fixture
                $fixture = Fixture::create([
                    'week' => $weekNum,
                    'home_id' => $home,
                    'away_id' => $away,
                ]);

                // set relations for eager loading
                $fixture->setRelation('home', $teamsKeyById[$home]);
                $fixture->setRelation('away', $teamsKeyById[$away]);

                // add to fixtures collection
                $fixtures->push($fixture);
            }
            $weekNum++;
        }

        // return sorted fixtures by week
        return $fixtures->sortBy('week')->values();
    }
}
