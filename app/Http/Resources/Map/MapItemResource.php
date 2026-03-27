<?php

namespace App\Http\Resources\Map;

use App\Models\Event;
use App\Models\Farm;
use App\Models\Ranch;
use App\Models\Ranche;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return match (true) {
            $this->resource instanceof Farm  => $this->farmData(),
            $this->resource instanceof Ranche => $this->ranchData(),
            $this->resource instanceof Event => $this->eventData(),
            default                          => [],
        };
    }

    private function farmData(): array
    {
        return [
            'id'           => $this->id,
            'type'         => 'farm',
            'name'         => $this->name,
            'address'      => $this->address,
            'city'         => $this->city,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'thumbnail'    => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'phone'        => $this->phone,
            'marker_color' => $this->marker_color,
            'marker_icon'  => $this->marker_icon,
            'status'       => $this->status,
            'is_featured'  => $this->is_featured,
            'owner_name' => $this->owner_name ?? null,
            'owner_address' => $this->owner_address ?? null,
            'owner_phone' => $this->owner_phone ?? null,
        ];
    }

    private function ranchData(): array
    {
        return [
            'id'           => $this->id,
            'type'         => 'ranch',
            'name'         => $this->name,
            'address'      => $this->address,
            'city'         => $this->city,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'thumbnail'    => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'phone'        => $this->phone,
            'marker_color' => $this->marker_color,
            'marker_icon'  => $this->marker_icon,
            'status'       => $this->status,
            'is_featured'  => $this->is_featured,
        ];
    }

    private function eventData(): array
    {
        return [
            'id'           => $this->id,
            'type'         => 'event',
            'name'         => $this->title,
            'address'      => $this->address,
            'city'         => $this->city,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'thumbnail'    => $this->image ? asset('storage/' . $this->image) : null,
            'phone'        => $this->phone,
            'marker_color' => '#E53935',   // events always red-ish
            'marker_icon'  => 'event_pin',
            'status'       => $this->status,
            'start_date'   => $this->start_date?->toIso8601String(),
            'end_date'     => $this->end_date?->toIso8601String(),
            'entry_fee'    => $this->entry_fee,
        ];
    }
}
