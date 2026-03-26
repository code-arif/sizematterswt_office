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

    /**
     * GET /api/v1/ranches
     * ─────────────────────────────────────────────────────────────────────
     * List + search ranches (paginated)
     *
     * Query params:
     *   ?search=sundown          → name / address / city / tags
     *   ?featured=1              → featured only
     *   ?per_page=15             → max 50
     */
    public function index(Request $request): JsonResponse
    {
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

    /**
     * GET /api/v1/ranches/{ranch}
     * ─────────────────────────────────────────────────────────────────────
     * Single ranch detail — includes media gallery
     */
    public function show($id): JsonResponse
    {
        $farm = Ranche::with('media')
            ->active()
            ->find($id);

        if (!$farm) {
            return $this->error('Ranche not found.', null, 404);
        }

        return $this->success('Ranche retrieved successfully.', [
            'farm' => new RancheResource($farm),
        ]);
    }
}
