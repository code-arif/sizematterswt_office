<?php

namespace App\Http\Resources\Farms;

use App\Models\Farm;
use App\Models\Favorite;
use App\Models\VisitedPlace;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $user = auth('api')->user();

        // Cache per-request so list (15 items) doesn't fire 30 extra queries
        $isFavorited = $user
            ? Favorite::where('user_id', $user->id)
            ->where('favoriteable_type', Farm::class)
            ->where('favoriteable_id', $this->id)
            ->exists()
            : false;

        $isVisited = $user
            ? VisitedPlace::where('user_id', $user->id)
            ->where('visitable_type', Farm::class)
            ->where('visitable_id', $this->id)
            ->exists()
            : false;

        return [
            'id'          => $this->id,
            'type'        => 'farm',
            'name'        => $this->name,
            'address'     => $this->address,
            'city'        => $this->city,
            'state'       => $this->state,
            'country'     => $this->country,
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'thumbnail'   => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'tags'        => $this->tags ?? [],
            'status'      => $this->status,
            'is_featured' => $this->is_featured,
            'phone'       => $this->phone,
            'marker_color' => $this->marker_color,
            'marker_icon' => $this->marker_icon,
            // Extra detail fields (only present on show endpoint)
            'description' => $this->when($request->routeIs('api.farms.show'), $this->description),
            'email'       => $this->when($request->routeIs('api.farms.show'), $this->email),
            'website'     => $this->when($request->routeIs('api.farms.show'), $this->website),
            'owner_name' => $this->owner_name ?? null,
            'owner_address' => $this->owner_address ?? null,
            'owner_phone' => $this->owner_phone ?? null,
            'owner_avatar'    => $this->owner_avatar ? asset('storage/' . $this->owner_avatar) : asset('admin/default/user.jpg'),

            // User state flags
            'is_favorite'  => $isFavorited,
            'is_visited'   => $isVisited,

            'media' => FarmMediaResource::collection(
                $this->whenLoaded('media')
            ),
        ];
    }
}
