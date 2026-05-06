<?php

namespace App\Http\Controllers\Api\Farm;

use App\Http\Controllers\Controller;
use App\Http\Resources\VisitedPlaceResource;
use App\Models\Event;
use App\Models\Farm;
use App\Models\Ranche;
use App\Models\VisitedPlace;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitedController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/visited
     * User's visited history (searchable, paginated)
     *
     * Query params:
     *   ?search=green
     *   ?type=farm
     *   ?per_page=15
     */
    public function index(Request $request): JsonResponse
    {
        $user  = auth('api')->user();
        $query = VisitedPlace::with('visitable')
            ->where('user_id', $user->id);

        // Filtering by type (supports 'type' or 'query_string' parameter)
        $filterType = $request->get('query_string') ?? $request->get('type');
        if ($filterType) {
            $typeMap = [
                'farm'  => Farm::class,
                'ranch' => Ranche::class,
                'event' => Event::class,
            ];

            // Support comma-separated types (e.g. ?query_string=farm,ranch)
            $types = explode(',', $filterType);
            $morphTypes = [];
            foreach ($types as $t) {
                $t = trim(strtolower($t));
                if (isset($typeMap[$t])) {
                    $morphTypes[] = $typeMap[$t];
                }
            }

            if (!empty($morphTypes)) {
                $query->whereIn('visitable_type', $morphTypes);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where(function ($sq) use ($search) {
                    $sq->where('visitable_type', Farm::class)
                        ->whereHasMorph(
                            'visitable',
                            Farm::class,
                            fn($m) =>
                            $m->where('name', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%")
                        );
                })
                    ->orWhere(function ($sq) use ($search) {
                        $sq->where('visitable_type', Ranche::class)
                            ->whereHasMorph(
                                'visitable',
                                Ranche::class,
                                fn($m) =>
                                $m->where('name', 'like', "%{$search}%")
                                    ->orWhere('city', 'like', "%{$search}%")
                            );
                    })
                    ->orWhere(function ($sq) use ($search) {
                        $sq->where('visitable_type', Event::class)
                            ->whereHasMorph(
                                'visitable',
                                Event::class,
                                fn($m) =>
                                $m->where('title', 'like', "%{$search}%")
                                    ->orWhere('city', 'like', "%{$search}%")
                            );
                    });
            });
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $visited = $query->orderByDesc('visited_at')->paginate($perPage);

        return $this->success('Visited places retrieved successfully.', [
            'visited'    => VisitedPlaceResource::collection($visited->items()),
            'pagination' => [
                'current_page' => $visited->currentPage(),
                'last_page'    => $visited->lastPage(),
                'per_page'     => $visited->perPage(),
                'total'        => $visited->total(),
            ],
        ]);
    }

    /**
     * POST /api/v1/visited
     * Mark a place as visited (idempotent — updates visited_at on repeat)
     *
     * Body: {
     *   "type": "farm|ranch|event",
     *   "id": 1,
     *   "latitude": 40.7,    // optional — user's GPS
     *   "longitude": -74.0,
     *   "note": "Great place!"
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type'      => ['required', 'in:farm,ranch,event'],
            'id'        => ['required', 'integer'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'note'      => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()->toArray(),
                'Validation failed',
                422
            );
        }

        $morphMap = [
            'farm'  => Farm::class,
            'ranch' => Ranche::class,
            'event' => Event::class,
        ];

        $morphType = $morphMap[$request->type];
        $model     = $morphType::find($request->id);

        if (! $model) {
            return $this->error(ucfirst($request->type) . ' not found.', null, 404);
        }

        $user = auth('api')->user();

        // Upsert: unique constraint is [user_id, visitable_type, visitable_id]
        $visited = VisitedPlace::updateOrCreate(
            [
                'user_id'       => $user->id,
                'visitable_type' => $morphType,
                'visitable_id'  => $request->id,
            ],
            [
                'visited_at' => now(),
                'latitude'   => $request->latitude,
                'longitude'  => $request->longitude,
                'note'       => $request->note,
            ]
        );

        $visited->load('visitable');

        return $this->success(
            'Place marked as visited.',
            ['visited' => new VisitedPlaceResource($visited)],
            201
        );
    }

    /**
     * DELETE /api/v1/visited/{visited}
     * Remove from visited history
     */
    public function destroy(VisitedPlace $visited): JsonResponse
    {
        $user = auth('api')->user();

        if ($visited->user_id !== $user->id) {
            return $this->error('Unauthorized.', null, 403);
        }

        $visited->delete();

        return $this->success('Removed from visited history.');
    }
}
