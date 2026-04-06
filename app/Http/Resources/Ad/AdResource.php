<?php

namespace App\Http\Resources\Ad;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Linked entity info (farm/ranch/event)
        $linked = null;
        if ($this->advertiseable) {
            $type = class_basename($this->advertiseable_type);
            $linked = [
                'type'      => strtolower($type),
                'id'        => $this->advertiseable->id,
                'name'      => $this->advertiseable->name ?? $this->advertiseable->title ?? null,
                'address'   => $this->advertiseable->address ?? null,
                'latitude'  => $this->advertiseable->latitude ?? null,
                'longitude' => $this->advertiseable->longitude ?? null,
                'thumbnail' => isset($this->advertiseable->thumbnail)
                                ? asset('storage/' . $this->advertiseable->thumbnail)
                                : (isset($this->advertiseable->image)
                                    ? asset('storage/' . $this->advertiseable->image)
                                    : null),
            ];
        }

        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'subtitle'     => $this->subtitle,
            'image'        => asset('storage/' . $this->image),
            'cta_label'    => $this->cta_label,

            // Distance in meters (calculated by Haversine SQL)
            // Round to nearest meter
            'distance_m'   => isset($this->distance) ? (int) round($this->distance) : null,
            'distance_label' => $this->formatDistance($this->distance ?? null),

            // Trigger info (mobile needs this to draw the circle)
            'trigger_latitude'  => $this->trigger_latitude,
            'trigger_longitude' => $this->trigger_longitude,
            'radius_meters'     => $this->radius_meters,

            'linked_place' => $linked,
        ];
    }

    private function formatDistance(?float $meters): ?string
    {
        if ($meters === null) return null;
        if ($meters < 1000) return round($meters) . ' m';
        return round($meters / 1000, 1) . ' km';
    }
}
