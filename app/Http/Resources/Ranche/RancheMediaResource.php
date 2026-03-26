<?php

namespace App\Http\Resources\Ranche;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RancheMediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'url'           => asset('storage/' . $this->file_path),
            'thumbnail_url' => $this->thumbnail_path
                                ? asset('storage/' . $this->thumbnail_path)
                                : null,
            'media_type'       => $this->media_type,
            'duration_seconds' => $this->duration_seconds,
            'caption'          => $this->caption,
            'is_cover'         => $this->is_cover,
            'sort_order'       => $this->sort_order,
        ];
    }
}
