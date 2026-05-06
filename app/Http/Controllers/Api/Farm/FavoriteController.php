<?php

namespace App\Http\Controllers\Api\Farm;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Event;
use App\Models\Farm;
use App\Models\Favorite;
use App\Models\Ranche;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/favorites
     * List user's favourites (searchable)
     *
     * Query params:
     *   ?search=green
     *   ?type=farm          (farm | ranch | event)
     *   ?per_page=15
     */
    public function index(Request $request): JsonResponse
    {
        $user  = auth('api')->user();
        $query = Favorite::with('favoriteable')
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
                $query->whereIn('favoriteable_type', $morphTypes);
            }
        }

        // Search inside the related model's name/title/address
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Farm
                $q->where(function ($sq) use ($search) {
                    $sq->where('favoriteable_type', Farm::class)
                        ->whereHasMorph('favoriteable', Farm::class, function ($m) use ($search) {
                            $m->where('name', 'like', "%{$search}%")
                                ->orWhere('city', 'like', "%{$search}%");
                        });
                })
                    // Ranch
                    ->orWhere(function ($sq) use ($search) {
                        $sq->where('favoriteable_type', Ranche::class)
                            ->whereHasMorph('favoriteable', Ranche::class, function ($m) use ($search) {
                                $m->where('name', 'like', "%{$search}%")
                                    ->orWhere('city', 'like', "%{$search}%");
                            });
                    })
                    // Event
                    ->orWhere(function ($sq) use ($search) {
                        $sq->where('favoriteable_type', Event::class)
                            ->whereHasMorph('favoriteable', Event::class, function ($m) use ($search) {
                                $m->where('title', 'like', "%{$search}%")
                                    ->orWhere('city', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $perPage    = min((int) $request->get('per_page', 15), 50);
        $favorites  = $query->latest()->paginate($perPage);

        return $this->success('Favorites retrieved successfully.', [
            'favorites'  => FavoriteResource::collection($favorites->items()),
            'pagination' => [
                'current_page' => $favorites->currentPage(),
                'last_page'    => $favorites->lastPage(),
                'per_page'     => $favorites->perPage(),
                'total'        => $favorites->total(),
            ],
        ]);
    }

    /**
     * POST /api/v1/favorites
     * Add an item to favourites (toggle — adds if absent, removes if present)
     *
     * Body: { "type": "farm|ranch|event", "id": 1 }
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'in:farm,ranch,event'],
            'id'   => ['required', 'integer'],
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

        $morphType  = $morphMap[$request->type];
        $model      = $morphType::find($request->id);

        if (! $model) {
            return $this->error(ucfirst($request->type) . ' not found.', null, 404);
        }

        $user = auth('api')->user();

        $existing = Favorite::where('user_id', $user->id)
            ->where('favoriteable_type', $morphType)
            ->where('favoriteable_id', $request->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return $this->success('Removed from favorites.', ['is_favorited' => false]);
        }

        Favorite::create([
            'user_id'          => $user->id,
            'favoriteable_type' => $morphType,
            'favoriteable_id'  => $request->id,
        ]);

        return $this->success('Added to favorites.', ['is_favorited' => true], 201);
    }

    /**
     * DELETE /api/v1/favorites/{favorite}
     * Remove a specific favourite record
     */
    public function destroy(Favorite $favorite): JsonResponse
    {
        $user = auth('api')->user();

        if ($favorite->user_id !== $user->id) {
            return $this->error('Unauthorized.', null, 403);
        }

        $favorite->delete();

        return $this->success('Removed from favorites.');
    }
}
