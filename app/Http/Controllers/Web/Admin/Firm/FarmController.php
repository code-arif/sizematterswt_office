<?php

namespace App\Http\Controllers\Web\Admin\Firm;

use App\Helpers\FileHandle;
use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Traits\AdminApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FarmController extends Controller
{
    use AdminApiResponse;

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/farms
    | Show the farms listing page (blade only — DataTable loads via AJAX)
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        return view('web.farms.index');
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/farms/datatable
    | Yajra DataTables JSON endpoint
    |--------------------------------------------------------------------------
    */
    public function datatable(Request $request): JsonResponse
    {
        $farms = Farm::with('admin')
            ->select('farms.*');

        return DataTables::of($farms)
            ->addIndexColumn()                          // DT_RowIndex → SR No.
            ->addColumn('admin_name', fn($row) => $row->admin?->profile->name ?? '—')
            ->addColumn('status_badge', function ($row) {
                $map = [
                    'active'   => 'bg-success-subtle text-success',
                    'inactive' => 'bg-secondary-subtle text-secondary',
                    'pending'  => 'bg-warning-subtle text-warning',
                ];
                $cls = $map[$row->status] ?? 'bg-secondary-subtle text-secondary';
                return '<span class="badge ' . $cls . '">' . ucfirst($row->status) . '</span>';
            })
            // ->addColumn('marker_preview', function ($row) {
            //     return '<span class="badge" style="background-color:' . e($row->marker_color) . ';color:#fff;">'
            //         . e($row->marker_icon) . '</span>';
            // })
            ->addColumn('action', function ($row) {
                return '
                <div class="d-flex align-items-center gap-1">
                    <a href="' . route('admin.farms.show', $row->id) . '"
                        class="btn btn-sm btn-soft-info" title="View">
                        <i class="ri-eye-fill"></i>
                    </a>
                    <a href="' . route('admin.farms.edit', $row->id) . '"
                        class="btn btn-sm btn-soft-primary" title="Edit">
                        <i class="ri-pencil-fill"></i>
                    </a>
                    <button type="button"
                        class="btn btn-sm btn-soft-danger delete-farm"
                        data-id="' . $row->id . '" title="Delete">
                        <i class="ri-delete-bin-fill"></i>
                    </button>
                </div>';
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/farms/create
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('web.farms.create');
    }

    /*
    |--------------------------------------------------------------------------
    | POST  /admin/farms
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): JsonResponse
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'address'      => ['required', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'zip_code'     => ['nullable', 'string', 'max:20'],
            'country'      => ['nullable', 'string', 'max:100'],
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:150'],
            'website'      => ['nullable', 'url', 'max:255'],
            'thumbnail'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tags'         => ['nullable', 'string'],   // comma-separated, parsed below
            'marker_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'marker_icon'  => ['nullable', 'string', 'max:50'],
            'status'       => ['nullable', 'in:active,inactive,pending'],
            'is_featured'  => ['nullable', 'boolean'],
            'media'        => ['nullable', 'array'],
            'media.*'      => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,avi,wmv', 'max:10240'],
            // ── Owner fields ──────────────────────────────────────────────
            'owner_name'    => ['nullable', 'string', 'max:150'],
            'owner_address' => ['nullable', 'string', 'max:255'],
            'owner_phone'   => ['nullable', 'string', 'max:20'],
            'owner_avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required'      => 'Farm name is required.',
            'address.required'   => 'Address is required.',
            'latitude.required'  => 'Please select a location on the map.',
            'longitude.required' => 'Please select a location on the map.',
            'latitude.between'   => 'Invalid latitude value.',
            'longitude.between'  => 'Invalid longitude value.',
            'thumbnail.image'    => 'Thumbnail must be an image.',
            'thumbnail.max'      => 'Thumbnail must not exceed 2 MB.',
            'marker_color.regex' => 'Marker color must be a valid hex code (e.g. #2E7D32).',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        // ── Handle thumbnail upload ────────────────────────────────────────
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = FileHandle::fileUpload($request->file('thumbnail'), 'farms/thumbnails');
            if (! $thumbnailPath) {
                return $this->error('Thumbnail upload failed. Please try again.', [], 500);
            }
        }

        $ownerAvatarPath = null;
        if ($request->hasFile('owner_avatar')) {
            $ownerAvatarPath = FileHandle::fileUpload($request->file('owner_avatar'), 'farms/owners');
            if (! $ownerAvatarPath) {
                return $this->error('Owner avatar upload failed. Please try again.', [], 500);
            }
        }

        // ── Parse tags (comma-separated string → array) ────────────────────
        $tags = null;
        if ($request->filled('tags')) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $request->tags))));
        }

        $farm = Farm::create([
            'admin_id'     => auth('admin')->id(),
            'name'         => $request->name,
            'description'  => $request->description,
            'address'      => $request->address,
            'city'         => $request->city,
            'state'        => $request->state,
            'zip_code'     => $request->zip_code,
            'country'      => $request->country ?? 'US',
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'website'      => $request->website,
            'thumbnail'    => $thumbnailPath,
            'tags'         => $tags,
            'marker_color' => $request->marker_color ?? '#2E7D32',
            'marker_icon'  => $request->marker_icon  ?? 'farm_pin',
            'status'       => $request->status       ?? 'active',
            'is_featured'  => $request->boolean('is_featured'),
            // ── Owner fields ──────────────────────────────────────────────
            'owner_name'    => $request->owner_name,
            'owner_address' => $request->owner_address,
            'owner_phone'   => $request->owner_phone,
            'owner_avatar'  => $ownerAvatarPath,
        ]);

        // ── Handle multiple media uploads ──────────────────────────────────
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $is_video = str_contains($file->getMimeType(), 'video');
                $folder = $is_video ? 'farms/videos' : 'farms/images';
                $path = FileHandle::fileUpload($file, $folder);

                if ($path) {
                    $farm->media()->create([
                        'file_path'  => $path,
                        'file_name'  => $file->getClientOriginalName(),
                        'mime_type'  => $file->getMimeType(),
                        'file_size'  => $file->getSize(),
                        'media_type' => $is_video ? 'video' : 'image',
                    ]);
                }
            }
        }

        return $this->success('Farm created successfully.', [
            'farm' => $farm,
            'redirect' => route('admin.farms.index'),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/farms/{farm}
    |--------------------------------------------------------------------------
    */
    // public function show(Farm $farm): View
    // {
    //     $farm->load(['media', 'admin']);
    //     return view('web.farms.show', compact('farm'));
    // }
    public function show($id)
    {
        // Ekhane 'media' eager load kora jate gallery thikmoto pay
        $farm = Farm::with('media', 'admin')->findOrFail($id);

        // return $farm;exit();

        return view('web.farms.show', compact('farm'));
    }



    /*
    |--------------------------------------------------------------------------
    | GET  /admin/farms/{farm}/edit
    |--------------------------------------------------------------------------
    */
    public function edit(Farm $farm): View
    {
        return view('web.farms.edit', compact('farm'));
    }

    /*
    |--------------------------------------------------------------------------
    | POST  /admin/farms/{farm}   (with _method=PUT)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Farm $farm): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:150'],
            'description'  => ['nullable', 'string', 'max:1000'],
            'address'      => ['required', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'state'        => ['nullable', 'string', 'max:100'],
            'zip_code'     => ['nullable', 'string', 'max:20'],
            'country'      => ['nullable', 'string', 'max:100'],
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'email'        => ['nullable', 'email', 'max:150'],
            'website'      => ['nullable', 'url', 'max:255'],
            'thumbnail'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'tags'         => ['nullable', 'string'],
            'marker_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'marker_icon'  => ['nullable', 'string', 'max:50'],
            'status'       => ['nullable', 'in:active,inactive,pending'],
            'is_featured'  => ['nullable', 'boolean'],
            'media'        => ['nullable', 'array'],
            'media.*'      => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,avi,wmv', 'max:10240'],
            // ── Owner fields ──────────────────────────────────────────────
            'owner_name'    => ['nullable', 'string', 'max:150'],
            'owner_address' => ['nullable', 'string', 'max:255'],
            'owner_phone'   => ['nullable', 'string', 'max:20'],
            'owner_avatar'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        // ── Handle new thumbnail ───────────────────────────────────────────
        $thumbnailPath = $farm->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if ($farm->thumbnail) {
                FileHandle::fileDelete($farm->thumbnail);
            }
            $thumbnailPath = FileHandle::fileUpload($request->file('thumbnail'), 'farms/thumbnails');
            if (! $thumbnailPath) {
                return $this->error('Thumbnail upload failed. Please try again.', [], 500);
            }
        }

        $ownerAvatarPath = $farm->owner_avatar;
        if ($request->hasFile('owner_avatar')) {
            if ($farm->owner_avatar) FileHandle::fileDelete($farm->owner_avatar);
            $ownerAvatarPath = FileHandle::fileUpload($request->file('owner_avatar'), 'farms/owners');
            if (! $ownerAvatarPath) {
                return $this->error('Owner avatar upload failed. Please try again.', [], 500);
            }
        }

        $tags = null;
        if ($request->filled('tags')) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $request->tags))));
        }

        $farm->update([
            'name'         => $request->name,
            'description'  => $request->description,
            'address'      => $request->address,
            'city'         => $request->city,
            'state'        => $request->state,
            'zip_code'     => $request->zip_code,
            'country'      => $request->country ?? 'US',
            'latitude'     => $request->latitude,
            'longitude'    => $request->longitude,
            'phone'        => $request->phone,
            'email'        => $request->email,
            'website'      => $request->website,
            'thumbnail'    => $thumbnailPath,
            'tags'         => $tags,
            'marker_color' => $request->marker_color ?? $farm->marker_color,
            'marker_icon'  => $request->marker_icon  ?? $farm->marker_icon,
            'status'       => $request->status       ?? $farm->status,
            'is_featured'  => $request->boolean('is_featured'),
            // ── Owner fields ──────────────────────────────────────────────
            'owner_name'    => $request->owner_name,
            'owner_address' => $request->owner_address,
            'owner_phone'   => $request->owner_phone,
            'owner_avatar'  => $ownerAvatarPath,
        ]);

        // ── Handle new media uploads ───────────────────────────────────────
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $is_video = str_contains($file->getMimeType(), 'video');
                $folder = $is_video ? 'farms/videos' : 'farms/images';
                $path = FileHandle::fileUpload($file, $folder);

                if ($path) {
                    $farm->media()->create([
                        'file_path'  => $path,
                        'file_name'  => $file->getClientOriginalName(),
                        'mime_type'  => $file->getMimeType(),
                        'file_size'  => $file->getSize(),
                        'media_type' => $is_video ? 'video' : 'image',
                    ]);
                }
            }
        }

        return $this->success('Farm updated successfully.', [
            'farm' => $farm->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE  /admin/farms/{farm}
    |--------------------------------------------------------------------------
    */
    public function destroy(Farm $farm): JsonResponse
    {
        if ($farm->thumbnail) {
            FileHandle::fileDelete($farm->thumbnail);
        }

        $farm->delete();   // soft delete

        return $this->success('Farm deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH  /admin/farms/{farm}/toggle-status
    |--------------------------------------------------------------------------
    */
    public function toggleStatus(Farm $farm): JsonResponse
    {
        $newStatus = $farm->status === 'active' ? 'inactive' : 'active';
        $farm->update(['status' => $newStatus]);

        return $this->success(
            'Farm status updated to ' . ucfirst($newStatus) . '.',
            ['status' => $newStatus]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH  /admin/farms/{farm}/toggle-featured
    |--------------------------------------------------------------------------
    */
    public function toggleFeatured(Farm $farm): JsonResponse
    {
        $farm->update(['is_featured' => ! $farm->is_featured]);

        return $this->success(
            $farm->is_featured ? 'Farm marked as featured.' : 'Farm removed from featured.',
            ['is_featured' => $farm->is_featured]
        );
    }

    /**
     * Remove a single media file from a farm
     */
    public function removeMedia(Farm $farm, $mediaId): JsonResponse
    {
        $media = $farm->media()->findOrFail($mediaId);

        if ($media->file_path) {
            FileHandle::fileDelete($media->file_path);
        }
        if ($media->thumbnail_path) {
            FileHandle::fileDelete($media->thumbnail_path);
        }

        $media->delete();

        return $this->success('Media deleted successfully.');
    }
}
