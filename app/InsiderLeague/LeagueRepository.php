<?php

namespace App\InsiderLeague;

use App\Models\Fixture;
use App\Models\Table;
use App\Models\Team;
use Illuminate\Support\Collection;

class LeagueRepository
{
    public function getRankedTable()
    {
        return Table::with("team")->ranked()->get();
    }

    public function generateFixtures(): Collection
    {

        $teams = Team::get();

        $teamsKeyById = $teams->keyBy('id');
        $teamIds = $teams->pluck('id')->toArray();

        /////////////////////////////////////

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

        $secondHalf = array_map(fn($week) => array_map(fn($m) => [$m[1], $m[0]], $week), $schedule);

        $fullSchedule = array_merge($schedule, $secondHalf);

        $weekNum = 1;
        $fixtures = collect();

        foreach ($fullSchedule as $week) {
            foreach ($week as [$home, $away]) {
                $fixture = Fixture::create([
                    'week' => $weekNum,
                    'home_id' => $home,
                    'away_id' => $away,
                ]);

                $fixture->setRelation('home', $teamsKeyById[$home]);
                $fixture->setRelation('away', $teamsKeyById[$away]);

                $fixtures->push($fixture);
            }
            $weekNum++;
        }


        return $fixtures->sortBy('week')->values();
    }
}
