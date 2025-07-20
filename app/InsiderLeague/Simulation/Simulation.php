<?php

namespace App\InsiderLeague\Simulation;

use Illuminate\Support\Facades\Pipeline;

class Simulation
{
    const SIMULATION_START_WEEK = 3;

    const LEAGUE_FINISH_WEEK = 6;

    protected $table;

    protected array $predictions = [];

    protected int $currentWeek = 0;

    protected bool $isChampionGuaranteed = false;

    protected int $remainingMatches = 0;

    protected int $maxPoints = 0;

    /**
     * Run the simulation.
     */
    public function simulate(): Simulation
    {
        return Pipeline::send($this)
            ->through([
                Phase\PrepareSimulationData::class,
                Phase\MarkGuaranteedChampions::class,
                Phase\RemoveEliminatedTeams::class,
                Phase\SimulateChampionship::class,
            ])
            ->thenReturn();
    }

    /**
     * Get the value of table
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set the value of table
     *
     * @return self
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get the value of isChampionGuaranteed
     */
    public function getIsChampionGuaranteed()
    {
        return $this->isChampionGuaranteed;
    }

    /**
     * Set the value of isChampionGuaranteed
     *
     * @return self
     */
    public function setIsChampionGuaranteed($isChampionGuaranteed)
    {
        $this->isChampionGuaranteed = $isChampionGuaranteed;

        return $this;
    }

    /**
     * Get the value of currentWeek
     */
    public function getCurrentWeek()
    {
        return $this->currentWeek;
    }

    /**
     * Set the value of currentWeek
     *
     * @return self
     */
    public function setCurrentWeek($currentWeek)
    {
        $this->currentWeek = $currentWeek;

        return $this;
    }

    /**
     * Get the value of remainingMatches
     */
    public function getRemainingMatches()
    {
        return $this->remainingMatches;
    }

    /**
     * Set the value of remainingMatches
     *
     * @return self
     */
    public function setRemainingMatches($remainingMatches)
    {
        $this->remainingMatches = $remainingMatches;

        return $this;
    }

    /**
     * Get the value of maxPoints
     */
    public function getMaxPoints()
    {
        return $this->maxPoints;
    }

    /**
     * Set the value of maxPoints
     *
     * @return self
     */
    public function setMaxPoints($maxPoints)
    {
        $this->maxPoints = $maxPoints;

        return $this;
    }

    /**
     * Get the value of predictions
     */
    public function getPredictions()
    {
        return $this->predictions;
    }

    /**
     * Set the value of predictions
     *
     * @return self
     */
    public function setPredictions($predictions)
    {
        $this->predictions = $predictions;

        return $this;
    }
}
