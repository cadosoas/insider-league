<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    /**
     * @var \App\Models\Table
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'team' => $this->resource->team->name,
            'points' => $this->resource->points,
            'played' => $this->resource->played,
            'wins' => $this->resource->wins,
            'draws' => $this->resource->draws,
            'losses' => $this->resource->losses,
            'goals_for' => $this->resource->goals_for,
            'goals_against' => $this->resource->goals_against,
            'goal_difference' => $this->resource->goal_difference,
        ];
    }
}
