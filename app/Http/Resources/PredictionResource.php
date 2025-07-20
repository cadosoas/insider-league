<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PredictionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'team' => $this['team']['name'] ?? null,
            'percentage' => $this['percentage'] ?? null,
            'possibility' => $this['possibility'] ?? null,
        ];
    }
}
