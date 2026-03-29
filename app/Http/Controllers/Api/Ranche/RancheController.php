<?php

namespace App\Http\Controllers\Api\Ranche;

use App\Http\Controllers\Controller;
use App\Http\Resources\Ranche\RancheResource;
use App\Models\Ranche;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RancheController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user  = auth('api')->user();
        $query = Ranche::query()->where('status', 'active');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhereJsonContains('tags', $search);
            });
        }

        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        // Eager load to avoid N+1
        if ($user) {
            $query->with([
                'favorites'     => fn($q) => $q->where('user_id', $user->id),
                'visitedPlaces' => fn($q) => $q->where('user_id', $user->id),
            ]);
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $ranches = $query->latest()->paginate($perPage);

        return $this->success('Ranches retrieved successfully.', [
            'ranches'    => RancheResource::collection($ranches->items()),
            'pagination' => [
                'current_page' => $ranches->currentPage(),
                'last_page'    => $ranches->lastPage(),
                'per_page'     => $ranches->perPage(),
                'total'        => $ranches->total(),
            ],
        ]);
    }

    public function show($id): JsonResponse
    {
        $user  = auth('api')->user();
        $query = Ranche::active();

        if ($user) {
            $query->with([
                'media',
                'favorites'     => fn($q) => $q->where('user_id', $user->id),
                'visitedPlaces' => fn($q) => $q->where('user_id', $user->id),
            ]);
        } else {
            $query->with('media');
        }

        $ranche = $query->find($id);

        if (! $ranche) {
            return $this->error('Ranche not found.', null, 404);
        }

        return $this->success('Ranche retrieved successfully.', [
            'ranche' => new RancheResource($ranche),
        ]);
    }
}
