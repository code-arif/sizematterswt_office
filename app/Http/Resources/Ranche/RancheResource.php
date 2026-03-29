<?php

namespace App\Http\Resources\Ranche;

use App\Models\Favorite;
use App\Models\VisitedPlace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RancheResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user     = auth('api')->user();
        $isDetail = $request->routeIs('api.ranches.show');

        $isFavorited = $user
            ? $this->relationLoaded('favorites') && $this->favorites->isNotEmpty()
            : false;

        $isVisited = $user
            ? $this->relationLoaded('visitedPlaces') && $this->visitedPlaces->isNotEmpty()
            : false;

        return [
            // ── Always present ────────────────────────────────────────────
            'id'           => $this->id,
            'type'         => 'ranche',
            'name'         => $this->name,
            'address'      => $this->address,
            'city'         => $this->city,
            'state'        => $this->state,
            'country'      => $this->country,
            'latitude'     => $this->latitude,
            'longitude'    => $this->longitude,
            'thumbnail'    => $this->thumbnail
                ? asset('storage/' . $this->thumbnail)
                : null,
            'tags'         => $this->tags ?? [],
            'acreage'      => $this->acreage,
            'status'       => $this->status,
            'is_featured'  => $this->is_featured,
            'phone'        => $this->phone,
            'marker_color' => $this->marker_color,
            'marker_icon'  => $this->marker_icon,

            // ── User state flags ──────────────────────────────────────────
            'is_favorite'  => $isFavorited,
            'is_visited'   => $isVisited,

            // ── Owner (always) ────────────────────────────────────────────
            'owner_name'    => $this->owner_name,
            'owner_address' => $this->owner_address,
            'owner_phone'   => $this->owner_phone,
            'owner_avatar'  => $this->owner_avatar
                ? asset('storage/' . $this->owner_avatar)
                : asset('admin/default/user.jpg'),

            // ── Detail-only fields ────────────────────────────────────────
            'description' => $this->when($isDetail, $this->description),
            'email'       => $this->when($isDetail, $this->email),
            'website'     => $this->when($isDetail, $this->website),
            'media'       => $this->when(
                $isDetail,
                RancheMediaResource::collection($this->whenLoaded('media'))
            ),
        ];
    }
}
