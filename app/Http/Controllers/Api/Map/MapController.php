<?php

namespace App\Http\Controllers\Api\Map;

use App\Http\Controllers\Controller;
use App\Http\Resources\Map\MapItemResource;
use App\Models\Event;
use App\Models\Farm;
use App\Models\Ranche;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MapController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/map
     * Global map markers — farms, ranches & events combined.
     *
     * Query params:
     *   ?type=farm,ranch,event   (comma-separated, omit = all)
     *   ?search=green valley
     *   ?bounds=lat_min,lng_min,lat_max,lng_max   (optional bounding box)
     *   ?featured=1
     */
    public function index(Request $request): JsonResponse
    {
        $types = $request->filled('type')
            ? array_map('trim', explode(',', $request->type))
            : ['farm', 'ranch', 'event'];

        $search   = $request->input('search');
        $featured = $request->boolean('featured');
        $bounds   = $this->parseBounds($request->input('bounds'));

        $items = new Collection();

        // ── Farms ─────────────────────────────────────────────────────────
        if (in_array('farm', $types)) {
            $query = Farm::where('status', 'active')
                ->select(
                    'id',
                    'name',
                    'address',
                    'city',
                    'latitude',
                    'longitude',
                    'thumbnail',
                    'phone',
                    'marker_color',
                    'marker_icon',
                    'status',
                    'is_featured',
                    'tags'
                );

            $this->applySearch($query, $search, 'name');
            $this->applyBounds($query, $bounds);
            if ($featured) $query->where('is_featured', true);

            $items = $items->merge($query->get());
        }

        // ── Ranches ───────────────────────────────────────────────────────
        if (in_array('ranch', $types)) {
            $query = Ranche::where('status', 'active')
                ->select(
                    'id',
                    'name',
                    'address',
                    'city',
                    'latitude',
                    'longitude',
                    'thumbnail',
                    'phone',
                    'marker_color',
                    'marker_icon',
                    'status',
                    'is_featured',
                    'tags'
                );

            $this->applySearch($query, $search, 'name');
            $this->applyBounds($query, $bounds);
            if ($featured) $query->where('is_featured', true);

            $items = $items->merge($query->get());
        }

        // ── Events ────────────────────────────────────────────────────────
        if (in_array('event', $types)) {
            $query = Event::whereIn('status', ['upcoming', 'ongoing'])
                ->select(
                    'id',
                    'title',
                    'address',
                    'city',
                    'latitude',
                    'longitude',
                    'image',
                    'phone',
                    'status',
                    'start_date',
                    'end_date',
                    'entry_fee'
                );

            $this->applySearch($query, $search, 'title');
            $this->applyBounds($query, $bounds);

            $items = $items->merge($query->get());
        }

        return $this->success('Map items retrieved successfully.', [
            'total' => $items->count(),
            'items' => MapItemResource::collection($items),
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function applySearch($query, ?string $search, string $nameField): void
    {
        if (! $search) return;
        $query->where(function ($q) use ($search, $nameField) {
            $q->where($nameField, 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%")
                ->orWhere('city', 'like', "%{$search}%");
        });
    }

    private function applyBounds($query, ?array $bounds): void
    {
        if (! $bounds) return;
        $query->whereBetween('latitude',  [$bounds['lat_min'], $bounds['lat_max']])
            ->whereBetween('longitude', [$bounds['lng_min'], $bounds['lng_max']]);
    }

    /** Parse "lat_min,lng_min,lat_max,lng_max" string */
    private function parseBounds(?string $boundsString): ?array
    {
        if (! $boundsString) return null;

        $parts = array_map('floatval', explode(',', $boundsString));
        if (count($parts) !== 4) return null;

        return [
            'lat_min' => min($parts[0], $parts[2]),
            'lat_max' => max($parts[0], $parts[2]),
            'lng_min' => min($parts[1], $parts[3]),
            'lng_max' => max($parts[1], $parts[3]),
        ];
    }
}
