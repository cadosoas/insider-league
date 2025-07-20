<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $team_id
 * @property int $played
 * @property int $wins
 * @property int $draws
 * @property int $losses
 * @property int $goals_for
 * @property int $goals_against
 * @property int $goal_difference
 * @property int $points
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property Team $team
 * @method static Builder ranked()
 */
class Table extends Model
{
    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = null;

    protected $fillable = [
        "team_id",
        "played",
        "wins",
        "draws",
        "losses",
        "goals_for",
        "goals_against",
        "goal_difference",
        "points",
        "updated_at",
    ];

    /**
     * Get the team associated with the table.
     *
     * @return BelongsTo<Team,$this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Scope a query to order tables by points, goal difference, and goals for.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRanked($query): Builder
    {
        return $query
            ->orderByDesc('points')
            ->orderByDesc('goal_difference')
            ->orderByDesc('goals_for');
    }
}
