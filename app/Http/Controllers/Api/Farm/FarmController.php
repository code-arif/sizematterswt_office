<?php

namespace App\Http\Controllers\Api\Farm;

use App\Http\Controllers\Controller;
use App\Http\Resources\Farms\FarmResource;
use App\Models\Farm;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FarmController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/farms
     * List + search farms (paginated)
     */
    public function index(Request $request): JsonResponse
    {
        $user  = auth('api')->user();
        $query = Farm::query()->where('status', 'active');

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

        // Eager load user's favorites + visited to avoid N+1
        if ($user) {
            $query->with([
                'favorites' => fn($q) => $q->where('user_id', $user->id),
                'visitedPlaces' => fn($q) => $q->where('user_id', $user->id),
            ]);
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $farms   = $query->latest()->paginate($perPage);

        return $this->success('Farms retrieved successfully.', [
            'farms'      => FarmResource::collection($farms->items()),
            'pagination' => [
                'current_page' => $farms->currentPage(),
                'last_page'    => $farms->lastPage(),
                'per_page'     => $farms->perPage(),
                'total'        => $farms->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/farms/{farm}
     * Single farm detail (with media)
     */
    public function show($id): JsonResponse
    {
        $farm = Farm::with('media')
            ->active()
            ->find($id);

        if (!$farm) {
            return $this->error('Farm not found.', null, 404);
        }

        return $this->success('Farm retrieved successfully.', [
            'farm' => new FarmResource($farm),
        ]);
    }
}
