<?php

namespace Tests\Unit\InsiderLeague\Simulation;

use App\InsiderLeague\Simulation\Simulation;
use Tests\TestCase;

class SimulationTest extends TestCase
{
    public function test_simulation_initial_defaults()
    {
        $simulation = new Simulation();

        $this->assertSame(0, $simulation->getCurrentWeek());
        $this->assertFalse($simulation->getIsChampionGuaranteed());
        $this->assertSame([], $simulation->getPredictions());
        $this->assertSame(0, $simulation->getRemainingMatches());
        $this->assertSame(0, $simulation->getMaxPoints());
    }

    public function test_setters_and_getters_work_as_expected()
    {
        $simulation = new Simulation();

        $simulation->setCurrentWeek(4)
            ->setIsChampionGuaranteed(true)
            ->setRemainingMatches(3)
            ->setMaxPoints(15)
            ->setPredictions(['Galatasaray']);

        $this->assertSame(4, $simulation->getCurrentWeek());
        $this->assertTrue($simulation->getIsChampionGuaranteed());
        $this->assertSame(3, $simulation->getRemainingMatches());
        $this->assertSame(15, $simulation->getMaxPoints());
        $this->assertContains('Galatasaray', $simulation->getPredictions());
    }
}
