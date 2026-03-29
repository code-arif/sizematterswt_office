<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Http\Resources\Event\EventResource;
use App\Models\Event;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/v1/events
     * List + search events (paginated)
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $statuses = $request->filled('status')
            ? explode(',', $request->status)
            : ['upcoming', 'ongoing'];

        $query = Event::query()->whereIn('status', $statuses);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('free')) {
            $query->where(function ($q) {
                $q->whereNull('entry_fee')->orWhere('entry_fee', 0);
            });
        }

        if ($request->filled('from')) {
            $query->whereDate('start_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('start_date', '<=', $request->to);
        }

        // Eager load user's favorites + visited to avoid N+1
        if ($user) {
            $query->with([
                'favorites'     => fn($q) => $q->where('user_id', $user->id),
                'visitedPlaces' => fn($q) => $q->where('user_id', $user->id),
            ]);
        }

        $perPage = min((int) $request->get('per_page', 15), 50);
        $events  = $query->orderBy('start_date')->paginate($perPage);

        return $this->success('Events retrieved successfully.', [
            'events'     => EventResource::collection($events->items()),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'last_page'    => $events->lastPage(),
                'per_page'     => $events->perPage(),
                'total'        => $events->total(),
            ],
        ]);
    }

    /**
     * GET /api/v1/events/{event}
     * Single event detail — includes media + linked farm/ranch
     */
    public function show(Event $event): JsonResponse
    {
        // Block cancelled events from public view
        if ($event->status === 'cancelled') {
            return $this->error('Event not found.', null, 404);
        }

        if ($user = auth('api')->user()) {
            $event->load([
                'media',
                'eventable',
                'favorites'     => fn($q) => $q->where('user_id', $user->id),
                'visitedPlaces' => fn($q) => $q->where('user_id', $user->id),
            ]);
        } else {
            $event->load(['media', 'eventable']);
        }

        return $this->success('Event retrieved successfully.', [
            'event' => new EventResource($event),
        ]);
    }
}
