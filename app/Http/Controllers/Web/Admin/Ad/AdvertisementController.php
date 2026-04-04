<?php

namespace App\Http\Controllers\Web\Admin\Ad;

use App\Helpers\FileHandle;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Event;
use App\Models\Farm;
use App\Models\Ranche;
use App\Traits\AdminApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class AdvertisementController extends Controller
{
    use AdminApiResponse;

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/advertisements
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        return view('web.advertisements.index');
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/advertisements/datatable
    |--------------------------------------------------------------------------
    */
    public function datatable(Request $request): JsonResponse
    {
        $ads = Advertisement::with('admin', 'advertiseable')
            ->select('advertisements.*');

        return DataTables::of($ads)
            ->addIndexColumn()
            ->addColumn('admin_name', fn($row) => $row->admin?->profile?->name ?? '—')
            ->addColumn('linked_to', function ($row) {
                if (! $row->advertiseable) return '<span class="text-muted">—</span>';

                $name  = $row->advertiseable->name ?? $row->advertiseable->title ?? '—';
                $type  = class_basename($row->advertiseable_type);
                $color = match ($type) {
                    'Farm'  => 'success',
                    'Ranch' => 'warning',
                    'Event' => 'danger',
                    default => 'secondary',
                };
                return '<span class="badge bg-' . $color . '-subtle text-' . $color . '">'
                    . $type . '</span> <span class="ms-1">' . e($name) . '</span>';
            })
            ->addColumn(
                'radius_label',
                fn($row) => $row->radius_meters >= 1000
                    ? round($row->radius_meters / 1000, 1) . ' km'
                    : $row->radius_meters . ' m'
            )
            ->addColumn('schedule', function ($row) {
                $from = $row->starts_at ? $row->starts_at->format('d M Y') : 'Always';
                $to   = $row->ends_at   ? $row->ends_at->format('d M Y')   : '∞';
                return $from . ' – ' . $to;
            })
            ->addColumn('status_badge', function ($row) {
                if ($row->is_expired) {
                    return '<span class="badge bg-danger-subtle text-danger">Expired</span>';
                }
                $map = [
                    'active'   => 'bg-success-subtle text-success',
                    'inactive' => 'bg-secondary-subtle text-secondary',
                ];
                $cls = $map[$row->status] ?? 'bg-secondary-subtle text-secondary';
                return '<span class="badge ' . $cls . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                <div class="d-flex align-items-center gap-1">
                    <a href="' . route('admin.advertisements.show', $row->id) . '"
                        class="btn btn-sm btn-soft-info" title="View">
                        <i class="ri-eye-fill"></i>
                    </a>
                    <a href="' . route('admin.advertisements.edit', $row->id) . '"
                        class="btn btn-sm btn-soft-primary" title="Edit">
                        <i class="ri-pencil-fill"></i>
                    </a>
                    <button type="button"
                        class="btn btn-sm btn-soft-danger delete-ad"
                        data-id="' . $row->id . '" title="Delete">
                        <i class="ri-delete-bin-fill"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['linked_to', 'status_badge', 'action'])
            ->make(true);
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/advertisements/create
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        $farms  = Farm::where('status', 'active')->orderBy('name')
            ->get(['id', 'name', 'latitude', 'longitude'])
            ->map(fn($f) => ['id' => $f->id, 'name' => $f->name, 'lat' => $f->latitude, 'lng' => $f->longitude]);

        $ranches = Ranche::where('status', 'active')->orderBy('name')
            ->get(['id', 'name', 'latitude', 'longitude'])
            ->map(fn($r) => ['id' => $r->id, 'name' => $r->name, 'lat' => $r->latitude, 'lng' => $r->longitude]);

        $events = Event::whereIn('status', ['upcoming', 'ongoing'])->orderBy('title')
            ->get(['id', 'title', 'latitude', 'longitude'])
            ->map(fn($e) => ['id' => $e->id, 'name' => $e->title, 'lat' => $e->latitude, 'lng' => $e->longitude]);

        return view('web.advertisements.create', compact('farms', 'ranches', 'events'));
    }

    /*
    |--------------------------------------------------------------------------
    | POST  /admin/advertisements
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'             => ['required', 'string', 'max:150'],
            'subtitle'          => ['nullable', 'string', 'max:255'],
            'image'             => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            // 'cta_label'         => ['nullable', 'string', 'max:50'],
            'trigger_latitude'  => ['required', 'numeric', 'between:-90,90'],
            'trigger_longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters'     => ['required', 'integer', 'min:100', 'max:50000'],
            'status'            => ['nullable', 'in:active,inactive'],
            'starts_at'         => ['nullable', 'date'],
            'ends_at'           => ['nullable', 'date', 'after_or_equal:starts_at'],
            'linked_type'       => ['nullable', 'in:farm,ranch,event'],
            'linked_id'         => ['nullable', 'integer'],
        ], [
            'title.required'            => 'Ad title is required.',
            'image.required'            => 'Banner image is required.',
            'trigger_latitude.required' => 'Please pin a trigger location on the map.',
            'trigger_longitude.required' => 'Please pin a trigger location on the map.',
            'radius_meters.min'         => 'Minimum radius is 100 meters.',
            'radius_meters.max'         => 'Maximum radius is 50 km.',
            'ends_at.after_or_equal'    => 'End date must be on or after start date.',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        // Upload banner image
        $imagePath = FileHandle::fileUpload($request->file('image'), 'advertisements');
        if (! $imagePath) {
            return $this->error('Image upload failed. Please try again.', [], 500);
        }

        // Resolve morph type
        [$morphType, $morphId] = $this->resolveMorph(
            $request->linked_type,
            $request->linked_id
        );

        $ad = Advertisement::create([
            'user_id' => auth('admin')->id(),
            'advertiser' => 'admin',
            'advertiseable_type'  => $morphType,
            'advertiseable_id'    => $morphId,
            'title'               => $request->title,
            'subtitle'            => $request->subtitle,
            'image'               => $imagePath,
            // 'cta_label'           => $request->cta_label ?? 'View Details',
            'trigger_latitude'    => $request->trigger_latitude,
            'trigger_longitude'   => $request->trigger_longitude,
            'radius_meters'       => $request->radius_meters,
            'status'              => $request->status ?? 'active',
            'starts_at'           => $request->starts_at,
            'ends_at'             => $request->ends_at,
        ]);

        return $this->success('Advertisement created successfully.', [
            'advertisement' => $ad,
            'redirect'      => route('admin.advertisements.index'),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/advertisements/{advertisement}
    |--------------------------------------------------------------------------
    */
    public function show(Advertisement $advertisement): View
    {
        $advertisement->load(['admin', 'advertiseable', 'impressions']);
        $totalImpressions = $advertisement->impressions()->count();
        $dismissed        = $advertisement->impressions()->where('is_dismissed', true)->count();

        return view('web.advertisements.show', compact(
            'advertisement',
            'totalImpressions',
            'dismissed'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/advertisements/{advertisement}/edit
    |--------------------------------------------------------------------------
    */
    public function edit(Advertisement $advertisement): View
    {
        $farms  = Farm::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $ranches = Ranche::where('status', 'active')->orderBy('name')->get(['id', 'name']);
        $events = Event::whereIn('status', ['upcoming', 'ongoing'])->orderBy('title')->get(['id', 'title']);

        return view('web.advertisements.edit', compact(
            'advertisement',
            'farms',
            'ranches',
            'events'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | POST  /admin/advertisements/{advertisement}  (_method=PUT)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Advertisement $advertisement): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'             => ['required', 'string', 'max:150'],
            'subtitle'          => ['nullable', 'string', 'max:255'],
            'image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            // 'cta_label'         => ['nullable', 'string', 'max:50'],
            'trigger_latitude'  => ['required', 'numeric', 'between:-90,90'],
            'trigger_longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters'     => ['required', 'integer', 'min:100', 'max:50000'],
            'status'            => ['nullable', 'in:active,inactive'],
            'starts_at'         => ['nullable', 'date'],
            'ends_at'           => ['nullable', 'date', 'after_or_equal:starts_at'],
            'linked_type'       => ['nullable', 'in:farm,ranch,event'],
            'linked_id'         => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        // Handle new image
        $imagePath = $advertisement->image;
        if ($request->hasFile('image')) {
            FileHandle::fileDelete($advertisement->image);
            $imagePath = FileHandle::fileUpload($request->file('image'), 'advertisements');
            if (! $imagePath) {
                return $this->error('Image upload failed. Please try again.', [], 500);
            }
        }

        [$morphType, $morphId] = $this->resolveMorph(
            $request->linked_type,
            $request->linked_id
        );

        $advertisement->update([
            'advertiseable_type'  => $morphType,
            'advertiseable_id'    => $morphId,
            'title'               => $request->title,
            'subtitle'            => $request->subtitle,
            'image'               => $imagePath,
            // 'cta_label'           => $request->cta_label ?? $advertisement->cta_label,
            'trigger_latitude'    => $request->trigger_latitude,
            'trigger_longitude'   => $request->trigger_longitude,
            'radius_meters'       => $request->radius_meters,
            'status'              => $request->status ?? $advertisement->status,
            'starts_at'           => $request->starts_at,
            'ends_at'             => $request->ends_at,
        ]);

        return $this->success('Advertisement updated successfully.', [
            'advertisement' => $advertisement->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE  /admin/advertisements/{advertisement}
    |--------------------------------------------------------------------------
    */
    public function destroy(Advertisement $advertisement): JsonResponse
    {
        FileHandle::fileDelete($advertisement->image);
        $advertisement->delete();

        return $this->success('Advertisement deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH  /admin/advertisements/{advertisement}/toggle-status
    |--------------------------------------------------------------------------
    */
    public function toggleStatus(Advertisement $advertisement): JsonResponse
    {
        $newStatus = $advertisement->status === 'active' ? 'inactive' : 'active';
        $advertisement->update(['status' => $newStatus]);

        return $this->success(
            'Status updated to ' . ucfirst($newStatus) . '.',
            ['status' => $newStatus]
        );
    }

    // Private helpers

    private function resolveMorph(?string $type, ?int $id): array
    {
        if (! $type || ! $id) return [null, null];

        $morphMap = [
            'farm'  => Farm::class,
            'ranch' => Ranche::class,
            'event' => Event::class,
        ];

        return [$morphMap[$type] ?? null, $id];
    }
}
