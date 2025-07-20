<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Table;

/**
 * @property int $id
 * @property string $name
 * @property int $strength
 * @property Table|null $table
 */
class Team extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        "name",
        "strength",
    ];

    public function table(): HasOne
    {
        return $this->hasOne(Table::class, 'team_id');
    }
}
