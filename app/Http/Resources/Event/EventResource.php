<?php

namespace App\Http\Resources\Event;

use App\Models\Farm;
use App\Models\Ranche;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user     = auth('api')->user();
        $isDetail = $request->routeIs('api.events.show');

        $isFavorited = $user
            ? $this->relationLoaded('favorites') && $this->favorites->isNotEmpty()
            : false;

        $isVisited = $user
            ? $this->relationLoaded('visitedPlaces') && $this->visitedPlaces->isNotEmpty()
            : false;

        return [
            // Always present (list + detail)
            'id'           => $this->id,
            'type'         => 'event',
            'title'        => $this->title,
            'address'      => $this->address,
            'city'         => $this->city,
            'state'        => $this->state,
            'country'      => $this->country,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'image'        => $this->image
                ? asset('storage/' . $this->image)
                : null,
            'start_date'   => $this->start_date?->toIso8601String(),
            'end_date'     => $this->end_date?->toIso8601String(),
            'is_free'      => $this->is_free,
            'entry_fee'    => $this->entry_fee,
            'capacity'     => $this->capacity,
            'status'       => $this->status,
            'phone'        => $this->phone,
            'marker_color' => '#E53935',   // events always use this color on map
            'marker_icon'  => 'event_pin',

            // Owner info (always — needed for list card UI)
            'owner' => [
                'name'    => $this->owner_name,
                'address' => $this->owner_address,
                'phone'   => $this->owner_phone,
                'avatar'  => $this->owner_avatar
                    ? asset('storage/' . $this->owner_avatar)
                    : null,
            ],

            // User state flags
            'is_favorite'  => $isFavorited,
            'is_visited'   => $isVisited,

            // Detail-only fields
            'description' => $this->when($isDetail, $this->description),
            'email'       => $this->when($isDetail, $this->email),
            'website'     => $this->when($isDetail, $this->website),

            // Linked farm/ranch (if any)
            'linked_place' => $this->when($isDetail, function () {
                $eventable = $this->whenLoaded('eventable');
                if (! $eventable || ! $this->eventable) return null;

                return [
                    'type' => match (true) {
                        $this->eventable instanceof Farm  => 'farm',
                        $this->eventable instanceof Ranche => 'ranch',
                        default                                        => 'unknown',
                    },
                    'id'        => $this->eventable->id,
                    'name'      => $this->eventable->name,
                    'address'   => $this->eventable->address,
                    'thumbnail' => $this->eventable->thumbnail
                        ? asset('storage/' . $this->eventable->thumbnail)
                        : null,
                ];
            }),

            'media' => $this->when(
                $isDetail,
                EventMediaResource::collection($this->whenLoaded('media'))
            ),
        ];
    }
}
