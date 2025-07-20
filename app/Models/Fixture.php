<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $week
 * @property int $home_id
 * @property int $away_id
 * @property int|null $home_score
 * @property int|null $away_score
 * @property \Illuminate\Support\Carbon|null $played_at
 * @property-read \App\Models\Team $home
 * @property-read \App\Models\Team $away
 *
 * @method static Builder|Fixture unplayed()
 * @method static Builder|Fixture played()
 * @method static Builder|Fixture forWeek(int $week)
 */
class Fixture extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'week',
        'home_id',
        'away_id',
        'home_score',
        'away_score',
        'played_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'played_at' => 'datetime',
    ];

    /**
     * Get the home team associated with the fixture.
     *
     * @return BelongsTo<Team,$this>
     */
    public function home(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_id');
    }

    /**
     * Get the away team associated with the fixture.
     *
     * @return BelongsTo<Team,$this>
     */
    public function away(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_id');
    }

    /**
     * Scope a query to only include unplayed fixtures.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeUnplayed($query)
    {
        return $query->whereNull('played_at');
    }

    /**
     *  Scope a query to only include played fixtures.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopePlayed($query)
    {
        return $query->whereNotNull('played_at');
    }

    /**
     * Scope a query to only include fixtures for a specific week.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeForWeek($query, int $week)
    {
        return $query->where('week', $week);
    }

    /**
     * Play the fixture and determine the scores based on team strengths.
     *
     * This method simulates a match between the home and away teams,
     * assigning scores based on their relative strengths.
     *
     * @return void
     */
    public function play()
    {
        $homeStrength = $this->home->strength;
        $awayStrength = $this->away->strength;

        $totalStrength = $homeStrength + $awayStrength;
        $homeChance = $homeStrength / $totalStrength;
        $awayChance = $awayStrength / $totalStrength;

        $homeScore = rand(0, 5);
        $awayScore = rand(0, 5);

        if (rand(0, 100) < $homeChance * 100) {
            $homeScore += rand(1, 3);
        } else {
            $awayScore += rand(1, 3);
        }

        $this->home_score = $homeScore;
        $this->away_score = $awayScore;
    }
}
