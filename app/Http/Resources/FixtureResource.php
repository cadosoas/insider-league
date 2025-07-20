<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixtureResource extends JsonResource
{
    /**
     * @var \App\Models\Fixture
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
            'week' => $this->resource->week,
            'home_team' => $this->resource->home->name ?? null,
            'away_team' => $this->resource->away->name ?? null,
            'home_score' => $this->resource->home_score,
            'away_score' => $this->resource->away_score,
            'played_at' => optional($this->resource->played_at)->toDateTimeString(),
        ];
    }
}
