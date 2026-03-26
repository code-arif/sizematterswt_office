<?php

namespace App\Http\Resources;

use App\Http\Resources\Map\MapItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitedPlaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->visitable;

        return [
            'id'         => $this->id,
            'visited_at' => $this->visited_at->toIso8601String(),
            'note'       => $this->note,
            'user_lat'   => $this->latitude,
            'user_lng'   => $this->longitude,
            'item'       => $item ? new MapItemResource($item) : null,
        ];
    }
}
