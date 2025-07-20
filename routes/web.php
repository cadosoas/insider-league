<?php

use App\Http\Controllers\LeagueController;
use App\Http\Controllers\SimulateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
});

Route::prefix('league')
    ->group(function () {
        Route::get('/teams', [LeagueController::class, 'teams'])->name('league.teams');
        Route::get('/tables', [LeagueController::class, 'tables'])->name('league.tables');
        Route::get('/fixtures', [LeagueController::class, 'fixtures'])->name('league.fixtures');
        Route::get('/generate-fixtures', [LeagueController::class, 'generateFixtures'])->name('league.generate-fixtures');
        Route::get('/reset', [LeagueController::class, 'reset'])->name('league.reset');

        Route::prefix('simulate')
            ->group(function () {
                Route::get('/', SimulateController::class)->name('league.simulate');
                Route::get('/play-all-weeks', [SimulateController::class, 'playAllWeeks'])->name('league.play-all-weeks');
                Route::get('/play-week-by-week', [SimulateController::class, 'playWeekByWeek'])->name('league.play-week-by-week');
            });
    })
    ->name('league.');
