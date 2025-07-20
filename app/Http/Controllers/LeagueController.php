<?php

namespace App\Http\Controllers;

use App\Http\Resources\FixtureResource;
use App\Http\Resources\TableResource;
use App\Http\Resources\TeamResource;
use App\InsiderLeague\FixtureRepository;
use App\InsiderLeague\LeagueRepository;
use App\Models\Fixture;
use App\Models\Table;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LeagueController extends Controller
{
    public function __construct(
        protected LeagueRepository $leagueRepository,
        protected FixtureRepository $fixtureRepository
    ) {}

    /**
     * Display the teams in the league.
     *
     * @return AnonymousResourceCollection
     */
    public function teams(): AnonymousResourceCollection
    {
        return TeamResource::collection(Team::get());
    }

    /**
     * Display the ranked tables of the league.
     *
     * @return AnonymousResourceCollection
     */
    public function tables(): AnonymousResourceCollection
    {
        return TableResource::collection(
            $this->leagueRepository->getRankedTable()
        );
    }

    /**
     * Display the fixtures of the league.
     *
     * @param  \Illuminate\Support\Collection|null  $fixtures
     * @return JsonResponse
     */
    public function fixtures($fixtures = null): JsonResponse
    {
        $fixtures = $fixtures ?? $this->fixtureRepository->getList();

        $grouped = $fixtures->groupBy('week')->map(function ($items) {
            return FixtureResource::collection($items);
        });

        return response()->json($grouped->values());
    }

    /**
     * Generate fixtures for the league.
     *
     * @return JsonResponse
     */
    public function generateFixtures(): JsonResponse
    {
        $fixtures = $this->fixtureRepository->getList();

        if ($fixtures->isNotEmpty()) {
            return $this->fixtures($fixtures);
        }

        $generatedFixtures = $this->leagueRepository->generateFixtures();

        return $this->fixtures($generatedFixtures);
    }

    /**
     * Reset the league by truncating fixtures and tables,
     *
     * @return JsonResponse
     */
    public function reset(): JsonResponse
    {
        Fixture::truncate();
        Table::truncate();

        foreach (Team::all() as $team) {
            Table::create([
                'team_id' => $team->id,
            ]);
        }

        return response()->json([
            "message" => "League reset successfully.",
        ]);
    }
}
