<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ?? null,
            'name' => $this->name ?? '',
            'username' => $this->username ?? '',
            'avatar' => $this->avatar
                ? url(Storage::url($this->avatar))
                : asset('admin/default/user.jpg'),
        ];
    }
}
