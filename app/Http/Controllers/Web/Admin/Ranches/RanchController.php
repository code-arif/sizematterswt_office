<?php

namespace App\Http\Controllers\Web\Admin\Ranches;

use App\Helpers\FileHandle;
use App\Http\Controllers\Controller;
use App\Models\Ranche;
use App\Traits\AdminApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class RanchController extends Controller
{
    use AdminApiResponse;

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/ranches
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        return view('web.ranches.index');
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/ranches/datatable
    |--------------------------------------------------------------------------
    */
    public function datatable(Request $request): JsonResponse
    {
        $ranches = Ranche::with('admin')->select('ranches.*');

        return DataTables::of($ranches)
            ->addIndexColumn()
            ->addColumn('admin_name', fn($row) => $row->admin?->name ?? '—')
            ->addColumn('acreage_display', fn($row) => $row->acreage
                ? number_format($row->acreage, 2) . ' ac'
                : '—')
            ->addColumn('status_badge', function ($row) {
                $map = [
                    'active'   => 'bg-success-subtle text-success',
                    'inactive' => 'bg-secondary-subtle text-secondary',
                    'pending'  => 'bg-warning-subtle text-warning',
                ];
                $cls = $map[$row->status] ?? 'bg-secondary-subtle text-secondary';
                return '<span class="badge ' . $cls . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('marker_preview', function ($row) {
                return '<span class="badge" style="background-color:' . e($row->marker_color) . ';color:#fff;">'
                    . e($row->marker_icon ?? 'ranch_pin') . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                <div class="d-flex align-items-center gap-1">
                    <a href="' . route('admin.ranches.show', $row->id) . '"
                        class="btn btn-sm btn-soft-info" title="View">
                        <i class="ri-eye-fill"></i>
                    </a>
                    <a href="' . route('admin.ranches.edit', $row->id) . '"
                        class="btn btn-sm btn-soft-primary" title="Edit">
                        <i class="ri-pencil-fill"></i>
                    </a>
                    <button type="button"
                        class="btn btn-sm btn-soft-danger delete-ranch"
                        data-id="' . $row->id . '" title="Delete">
                        <i class="ri-delete-bin-fill"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['status_badge', 'marker_preview', 'action'])
            ->make(true);
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/ranches/create
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('web.ranches.create');
    }

    /*
    |--------------------------------------------------------------------------
    | POST  /admin/ranches
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'          => ['required', 'string', 'max:150'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'address'       => ['required', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'zip_code'      => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:100'],
            'latitude'      => ['required', 'numeric', 'between:-90,90'],
            'longitude'     => ['required', 'numeric', 'between:-180,180'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:150'],
            'website'       => ['nullable', 'url', 'max:255'],
            'thumbnail'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tags'          => ['nullable', 'string'],
            'acreage'       => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'marker_color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'marker_icon'   => ['nullable', 'string', 'max:50'],
            'status'        => ['nullable', 'in:active,inactive,pending'],
            'is_featured'   => ['nullable', 'boolean'],
            // ── Owner fields ──────────────────────────────────────────────
            'owner_name'    => ['nullable', 'string', 'max:150'],
            'owner_address' => ['nullable', 'string', 'max:255'],
            'owner_phone'   => ['nullable', 'string', 'max:20'],
            'owner_avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required'      => 'Ranch name is required.',
            'address.required'   => 'Address is required.',
            'latitude.required'  => 'Please select a location on the map.',
            'longitude.required' => 'Please select a location on the map.',
            'acreage.numeric'    => 'Acreage must be a valid number.',
            'thumbnail.max'      => 'Thumbnail must not exceed 2 MB.',
            'marker_color.regex' => 'Marker color must be a valid hex code (e.g. #8D4E0B).',
            'owner_avatar.max'   => 'Owner avatar must not exceed 2 MB.',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = FileHandle::fileUpload($request->file('thumbnail'), 'ranches/thumbnails');
            if (! $thumbnailPath) {
                return $this->error('Thumbnail upload failed. Please try again.', [], 500);
            }
        }

        $ownerAvatarPath = null;
        if ($request->hasFile('owner_avatar')) {
            $ownerAvatarPath = FileHandle::fileUpload($request->file('owner_avatar'), 'ranches/owners');
            if (! $ownerAvatarPath) {
                return $this->error('Owner avatar upload failed. Please try again.', [], 500);
            }
        }

        $tags = null;
        if ($request->filled('tags')) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $request->tags))));
        }

        $ranch = Ranche::create([
            'admin_id'      => auth('admin')->id(),
            'name'          => $request->name,
            'description'   => $request->description,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'zip_code'      => $request->zip_code,
            'country'       => $request->country ?? 'US',
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'website'       => $request->website,
            'thumbnail'     => $thumbnailPath,
            'tags'          => $tags,
            'acreage'       => $request->acreage,
            'marker_color'  => $request->marker_color ?? '#8D4E0B',
            'marker_icon'   => $request->marker_icon  ?? 'ranch_pin',
            'status'        => $request->status       ?? 'active',
            'is_featured'   => $request->boolean('is_featured'),
            // ── Owner fields ──────────────────────────────────────────────
            'owner_name'    => $request->owner_name,
            'owner_address' => $request->owner_address,
            'owner_phone'   => $request->owner_phone,
            'owner_avatar'  => $ownerAvatarPath,
        ]);

        return $this->success('Ranch created successfully.', [
            'ranch'    => $ranch,
            'redirect' => route('admin.ranches.index'),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/ranches/{ranch}
    |--------------------------------------------------------------------------
    */
public function show($id)
{
    // Ekhane 'media' eager load kora jate gallery thikmoto pay
    $ranch = Ranche::with('media', 'admin')->findOrFail($id);

    return view('web.ranches.show', compact('ranch'));
}

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/ranches/{ranch}/edit
    |--------------------------------------------------------------------------
    */
    public function edit(Ranche $ranch): View
    {
        return view('web.ranches.edit', compact('ranch'));
    }

    /*
    |--------------------------------------------------------------------------
    | POST  /admin/ranches/{ranch}   (_method=PUT)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Ranche $ranch): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'          => ['required', 'string', 'max:150'],
            'description'   => ['nullable', 'string', 'max:1000'],
            'address'       => ['required', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'zip_code'      => ['nullable', 'string', 'max:20'],
            'country'       => ['nullable', 'string', 'max:100'],
            'latitude'      => ['required', 'numeric', 'between:-90,90'],
            'longitude'     => ['required', 'numeric', 'between:-180,180'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'email'         => ['nullable', 'email', 'max:150'],
            'website'       => ['nullable', 'url', 'max:255'],
            'thumbnail'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tags'          => ['nullable', 'string'],
            'acreage'       => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'marker_color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'marker_icon'   => ['nullable', 'string', 'max:50'],
            'status'        => ['nullable', 'in:active,inactive,pending'],
            'is_featured'   => ['nullable', 'boolean'],
            // ── Owner fields ──────────────────────────────────────────────
            'owner_name'    => ['nullable', 'string', 'max:150'],
            'owner_address' => ['nullable', 'string', 'max:255'],
            'owner_phone'   => ['nullable', 'string', 'max:20'],
            'owner_avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $thumbnailPath = $ranch->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($ranch->thumbnail) FileHandle::fileDelete($ranch->thumbnail);
            $thumbnailPath = FileHandle::fileUpload($request->file('thumbnail'), 'ranches/thumbnails');
            if (! $thumbnailPath) {
                return $this->error('Thumbnail upload failed. Please try again.', [], 500);
            }
        }

        $ownerAvatarPath = $ranch->owner_avatar;
        if ($request->hasFile('owner_avatar')) {
            if ($ranch->owner_avatar) FileHandle::fileDelete($ranch->owner_avatar);
            $ownerAvatarPath = FileHandle::fileUpload($request->file('owner_avatar'), 'ranches/owners');
            if (! $ownerAvatarPath) {
                return $this->error('Owner avatar upload failed. Please try again.', [], 500);
            }
        }

        $tags = null;
        if ($request->filled('tags')) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $request->tags))));
        }

        $ranch->update([
            'name'          => $request->name,
            'description'   => $request->description,
            'address'       => $request->address,
            'city'          => $request->city,
            'state'         => $request->state,
            'zip_code'      => $request->zip_code,
            'country'       => $request->country ?? 'US',
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'phone'         => $request->phone,
            'email'         => $request->email,
            'website'       => $request->website,
            'thumbnail'     => $thumbnailPath,
            'tags'          => $tags,
            'acreage'       => $request->acreage,
            'marker_color'  => $request->marker_color ?? $ranch->marker_color,
            'marker_icon'   => $request->marker_icon  ?? $ranch->marker_icon,
            'status'        => $request->status       ?? $ranch->status,
            'is_featured'   => $request->boolean('is_featured'),
            // ── Owner fields ──────────────────────────────────────────────
            'owner_name'    => $request->owner_name,
            'owner_address' => $request->owner_address,
            'owner_phone'   => $request->owner_phone,
            'owner_avatar'  => $ownerAvatarPath,
        ]);

        return $this->success('Ranch updated successfully.', [
            'ranch' => $ranch->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE  /admin/ranches/{ranch}
    |--------------------------------------------------------------------------
    */
    public function destroy(Ranche $ranch): JsonResponse
    {
        if ($ranch->thumbnail) FileHandle::fileDelete($ranch->thumbnail);
        if ($ranch->owner_avatar) FileHandle::fileDelete($ranch->owner_avatar);
        $ranch->delete();
        return $this->success('Ranch deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH  /admin/ranches/{ranch}/toggle-status
    |--------------------------------------------------------------------------
    */
    public function toggleStatus(Ranche $ranch): JsonResponse
    {
        $newStatus = $ranch->status === 'active' ? 'inactive' : 'active';
        $ranch->update(['status' => $newStatus]);
        return $this->success('Ranch status updated to ' . ucfirst($newStatus) . '.', ['status' => $newStatus]);
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH  /admin/ranches/{ranch}/toggle-featured
    |--------------------------------------------------------------------------
    */
    public function toggleFeatured(Ranche $ranch): JsonResponse
    {
        $ranch->update(['is_featured' => ! $ranch->is_featured]);
        return $this->success(
            $ranch->is_featured ? 'Ranch marked as featured.' : 'Ranch removed from featured.',
            ['is_featured' => $ranch->is_featured]
        );
    }
}
