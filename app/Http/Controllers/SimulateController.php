<?php

namespace App\Http\Controllers;

use App\Http\Resources\FixtureResource;
use App\Http\Resources\PredictionResource;
use App\Http\Resources\TableResource;
use App\InsiderLeague\FixtureRepository;
use App\InsiderLeague\LeagueRepository;
use App\InsiderLeague\Simulation\Simulation;
use App\Models\Fixture;
use App\Models\Table;

class SimulateController extends Controller
{
    public function __construct(
        protected LeagueRepository $leagueRepository,
        protected FixtureRepository $fixtureRepository
    ) {}

    /**
     * Handle the simulation of the league.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        // get table
        $table = $this->leagueRepository->getRankedTable();
        $firstTable = $table->first();

        // if the league is finished
        if ($firstTable->played == Simulation::LEAGUE_FINISH_WEEK) {
            return response()->json([
                'tables' => TableResource::collection($table),
                'fixtures' => [],
                'predictions' => [],
                'current_week' => $firstTable->played,
                'champion' => $firstTable->team->name,
            ]);
        }

        // get current week
        $currentWeek = $firstTable->played + 1;

        // get current week fixtures
        $currentFixture = Fixture::with(['home', 'away'])
            ->where('week', $currentWeek)
            ->get();

        // default predictions
        $predictions = [];

        foreach ($table as $team) {
            $predictions[] = [
                'team' => $team->team,
                'percentage' => 0,
                'possibility' => true,
            ];
        }

        // if the current week is less than or equal to SIMULATE_WEEK
        // start the simulation
        if ($currentWeek > Simulation::SIMULATION_START_WEEK) {
            $predictions = (new Simulation)
                ->setTable($table)
                ->setCurrentWeek($currentWeek)
                ->simulate()
                ->getPredictions();
        }

        return response()->json([
            'tables' => TableResource::collection($table),
            'fixtures' => FixtureResource::collection($currentFixture),
            'predictions' => PredictionResource::collection($predictions),
            'current_week' => $currentWeek,
            'champion' => null,
        ]);
    }

    /**
     * Play all fixtures for the league.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function playAllWeeks()
    {
        // get all tables
        $tables = Table::all()->keyBy('team_id');

        // get all unplayed fixtures
        $fixtures = Fixture::with(['home', 'away'])
            ->unplayed()
            ->get();

        // play all fixtures
        foreach ($fixtures as $fixture) {
            $this->fixtureRepository->playFixture($fixture, $tables);
        }

        // save all tables
        $tables->each->save();

        return $this->__invoke();
    }

    /**
     * Play fixtures week by week.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function playWeekByWeek()
    {
        // get all tables
        $tables = Table::all()->keyBy('team_id');

        // Get the next week fixtures
        $table = Table::first();
        $week = $table->played + 1;

        // get unplayed fixtures for the next week
        $fixtures = Fixture::with(['home', 'away'])
            ->unplayed()
            ->forWeek($week)
            ->get();

        // play all fixtures for the next week
        foreach ($fixtures as $fixture) {
            $this->fixtureRepository->playFixture($fixture, $tables);
        }

        // save all tables
        $tables->each->save();

        return $this->__invoke();
    }
}
