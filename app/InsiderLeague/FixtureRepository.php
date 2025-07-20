<?php

namespace App\InsiderLeague;

use App\Models\Fixture;
use Illuminate\Support\Collection;

class FixtureRepository
{
    public function getList(): Collection
    {
        return Fixture::with(['home', 'away'])->orderBy('week')->get();
    }

    public function playFixture(Fixture $fixture, Collection $tables): void
    {
        $fixture->play();

        $fixture->update([
            "home_score" => $fixture->home_score,
            "away_score" => $fixture->away_score,
            "played_at" => now(),
        ]);

        $homeScore = $fixture->home_score;
        $awayScore = $fixture->away_score;

        $homeTable = $tables[$fixture->home->id];
        $awayTable = $tables[$fixture->away->id];

        $homeTable->played += 1;
        $awayTable->played += 1;
        $homeTable->goals_for += $homeScore;
        $awayTable->goals_for += $awayScore;
        $homeTable->goals_against += $awayScore;
        $awayTable->goals_against += $homeScore;
        $homeTable->goal_difference = $homeTable->goals_for - $homeTable->goals_against;
        $awayTable->goal_difference = $awayTable->goals_for - $awayTable->goals_against;

        if ($homeScore > $awayScore) {
            $homeTable->wins += 1;
            $awayTable->losses += 1;
        } elseif ($homeScore < $awayScore) {
            $homeTable->losses += 1;
            $awayTable->wins += 1;
        } else {
            $homeTable->draws += 1;
            $awayTable->draws += 1;
        }

        $homeTable->points = $homeTable->wins * 3 + $homeTable->draws;
        $awayTable->points = $awayTable->wins * 3 + $awayTable->draws;
    }
}
