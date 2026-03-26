<?php

namespace App\Http\Resources\Farms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FarmResource extends JsonResource
{
    public function toArray(Request $request): array
    {
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
            'media'       => $this->when(
                $request->routeIs('api.farms.show'),
                FarmMediaResource::collection($this->whenLoaded('media'))
            ),
        ];
    }
}
