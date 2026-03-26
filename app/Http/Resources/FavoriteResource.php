<?php

namespace App\Http\Resources;

use App\Http\Resources\Map\MapItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->favoriteable;
        if (! $item) {
            return ['id' => $this->id, 'item' => null];
        }

        return [
            'id'         => $this->id,
            'saved_at'   => $this->created_at->toIso8601String(),
            'item'       => new MapItemResource($item),
        ];
    }
}
