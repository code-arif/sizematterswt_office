<?php

namespace App\Http\Controllers\Api\Ads;

use App\Http\Controllers\Controller;
use App\Http\Resources\Ad\AdResource;
use App\Models\AdImpression;
use App\Models\Advertisement;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    use ApiResponse;

    public function nearby(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude'  => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius'    => ['nullable', 'integer', 'min:100', 'max:50000'],
        ]);

        if ($validator->fails()) {
            return $this->validationError(
                $validator->errors()->toArray(),
                'Validation failed',
                422
            );
        }

        $lat    = (float) $request->latitude;
        $lng    = (float) $request->longitude;
        // Use ad's own radius_meters OR fallback to request radius
        // We query by each ad's individual radius below
        $defaultRadius = (int) $request->get('radius', 5000); // search window

        $ads = Advertisement::selectRaw("
                advertisements.*,
                (6371000 * acos(
                    LEAST(1.0, (
                        cos(radians(?)) * cos(radians(trigger_latitude))
                        * cos(radians(trigger_longitude) - radians(?))
                        + sin(radians(?)) * sin(radians(trigger_latitude))
                    ))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            // Each ad has its OWN radius — use that, not a global radius
            ->havingRaw('distance <= radius_meters')
            ->orderBy('distance')
            ->with('advertiseable')
            ->limit(3)
            ->get();

        // Track impressions (fire-and-forget)
        $user = auth('api')->user();
        foreach ($ads as $ad) {
            AdImpression::firstOrCreate(
                [
                    'advertisement_id' => $ad->id,
                    'user_id'          => $user?->id,
                ],
                [
                    'device_id'    => $request->header('X-Device-ID'),
                    'is_dismissed' => false,
                    'seen_at'      => now(),
                ]
            );
            // Increment counter (non-blocking)
            $ad->increment('impression_count');
        }

        return $this->success('Nearby ads fetched.', [
            'count' => $ads->count(),
            'ads'   => AdResource::collection($ads),
        ]);
    }

    public function dismiss(Advertisement $advertisement): JsonResponse
    {
        $user = auth('api')->user();

        AdImpression::updateOrCreate(
            [
                'advertisement_id' => $advertisement->id,
                'user_id'          => $user->id,
            ],
            [
                'is_dismissed' => true,
                'seen_at'      => now(),
            ]
        );

        return $this->success('Ad dismissed.');
    }
}
