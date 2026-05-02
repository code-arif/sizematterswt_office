<?php

namespace App\Http\Controllers\Web\Admin\Event;

use App\Helpers\FileHandle;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Notifications\EventNotification;
use App\Traits\AdminApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class EventController extends Controller
{
    use AdminApiResponse;

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/events
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        return view('web.events.index');
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/events/datatable
    |--------------------------------------------------------------------------
    */
    public function datatable(Request $request): JsonResponse
    {
        $events = Event::with('admin')->select('events.*');

        return DataTables::of($events)
            ->addIndexColumn()
            ->addColumn('admin_name', fn($row) => $row->admin?->name ?? '—')
            ->addColumn(
                'date_range',
                fn($row) =>
                $row->start_date?->format('M d, Y')
                    . ($row->end_date ? ' – ' . $row->end_date->format('M d, Y') : '')
            )
            ->addColumn(
                'entry_fee_display',
                fn($row) =>
                $row->entry_fee ? '$' . number_format($row->entry_fee, 2) : 'Free'
            )
            ->addColumn('status_badge', function ($row) {
                $map = [
                    'upcoming'   => 'bg-info-subtle text-info',
                    'ongoing'    => 'bg-success-subtle text-success',
                    'completed'  => 'bg-secondary-subtle text-secondary',
                    'cancelled'  => 'bg-danger-subtle text-danger',
                ];
                $cls = $map[$row->status] ?? 'bg-secondary-subtle text-secondary';
                return '<span class="badge ' . $cls . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                <div class="d-flex align-items-center gap-1">
                    <a href="' . route('admin.events.show', $row->id) . '"
                        class="btn btn-sm btn-soft-info" title="View">
                        <i class="ri-eye-fill"></i>
                    </a>
                    <a href="' . route('admin.events.edit', $row->id) . '"
                        class="btn btn-sm btn-soft-primary" title="Edit">
                        <i class="ri-pencil-fill"></i>
                    </a>
                    <button type="button"
                        class="btn btn-sm btn-soft-danger delete-event"
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
    | GET  /admin/events/create
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('web.events.create');
    }

    /*
    |--------------------------------------------------------------------------
    | POST  /admin/events
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'          => ['required', 'string', 'max:150'],
            'owner_name'          => ['required', 'string', 'max:150'],
            'owner_address'          => ['required', 'string', 'max:550'],
            'owner_phone'          => ['required', 'string', 'max:20'],
            'owner_avatar'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'address'        => ['required', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'max:100'],
            'zip_code'       => ['nullable', 'string', 'max:20'],
            'country'        => ['nullable', 'string', 'max:100'],
            'latitude'       => ['required', 'numeric', 'between:-90,90'],
            'longitude'      => ['required', 'numeric', 'between:-180,180'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:150'],
            'website'        => ['nullable', 'url', 'max:255'],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'entry_fee'      => ['nullable', 'numeric', 'min:0'],
            'capacity'       => ['nullable', 'integer', 'min:1'],
            'eventable_type' => ['nullable', 'string', 'max:100'],
            'eventable_id'   => ['nullable', 'integer'],
            'status'         => ['nullable', 'in:upcoming,ongoing,completed,cancelled'],
            'media'          => ['nullable', 'array'],
            'media.*'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:10240'], // 10MB
        ], [
            'title.required'      => 'Event title is required.',
            'address.required'    => 'Address is required.',
            'latitude.required'   => 'Please select a location on the map.',
            'longitude.required'  => 'Please select a location on the map.',
            'start_date.required' => 'Start date is required.',
            'end_date.after_or_equal' => 'End date must be on or after the start date.',
            'image.max'           => 'Image must not exceed 2 MB.',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = FileHandle::fileUpload($request->file('image'), 'events/images');
            if (! $imagePath) {
                return $this->error('Image upload failed. Please try again.', [], 500);
            }
        }
        $owner_avatar = null;
        if ($request->hasFile('owner_avatar')) {
            $owner_avatar = FileHandle::fileUpload($request->file('image'), 'events/images/owner_avatar/');
            if (! $owner_avatar) {
                return $this->error('Image upload failed. Please try again.', [], 500);
            }
        }

        $event = Event::create([
            'admin_id'       => auth('admin')->id(),
            'owner_name'          => $request->owner_name,
            'owner_address'          => $request->owner_address,
            'owner_phone'          => $request->owner_phone,
            'owner_avatar'          => $owner_avatar,
            'title'          => $request->title,
            'description'    => $request->description,
            'address'        => $request->address,
            'city'           => $request->city,
            'state'          => $request->state,
            'zip_code'       => $request->zip_code,
            'country'        => $request->country ?? 'US',
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'website'        => $request->website,
            'image'          => $imagePath,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'entry_fee'      => $request->entry_fee,
            'capacity'       => $request->capacity,
            'eventable_type' => $request->eventable_type,
            'eventable_id'   => $request->eventable_id,
            'status'         => $request->status ?? 'upcoming',
        ]);

        // Multiple Media Upload
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $type = str_contains($file->getMimeType(), 'video') ? 'video' : 'image';
                $path = FileHandle::fileUpload($file, 'events/' . ($type === 'video' ? 'videos' : 'images'));

                if ($path) {
                    $event->media()->create([
                        'file_path'  => $path,
                        'file_name'  => $file->getClientOriginalName(),
                        'mime_type'  => $file->getMimeType(),
                        'file_size'  => $file->getSize(),
                        'media_type' => $type,
                    ]);
                }
            }
        }

        // Send notification to all users
        $users = User::all();
        Notification::send($users, new EventNotification($event, 'created'));

        return $this->success('Event created successfully.', [
            'event'    => $event,
            'redirect' => route('admin.events.index'),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/events/{event}
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        // Ekhane 'media' eager load kora jate gallery thikmoto pay
        $event = Event::with('media', 'admin')->findOrFail($id);

        return view('web.events.show', compact('event'));
    }

    /*
    |--------------------------------------------------------------------------
    | GET  /admin/events/{event}/edit
    |--------------------------------------------------------------------------
    */
    public function edit(Event $event): View
    {
        return view('web.events.edit', compact('event'));
    }

    /*
    |--------------------------------------------------------------------------
    | POST  /admin/events/{event}   (_method=PUT)
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Event $event): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'owner_name'          => ['required', 'string', 'max:150'],
            'owner_address'          => ['required', 'string', 'max:550'],
            'owner_phone'          => ['required', 'string', 'max:20'],
            'owner_avatar'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'title'          => ['required', 'string', 'max:150'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'address'        => ['required', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'max:100'],
            'zip_code'       => ['nullable', 'string', 'max:20'],
            'country'        => ['nullable', 'string', 'max:100'],
            'latitude'       => ['required', 'numeric', 'between:-90,90'],
            'longitude'      => ['required', 'numeric', 'between:-180,180'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'email'          => ['nullable', 'email', 'max:150'],
            'website'        => ['nullable', 'url', 'max:255'],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'entry_fee'      => ['nullable', 'numeric', 'min:0'],
            'capacity'       => ['nullable', 'integer', 'min:1'],
            'eventable_type' => ['nullable', 'string', 'max:100'],
            'eventable_id'   => ['nullable', 'integer'],
            'status'         => ['nullable', 'in:upcoming,ongoing,completed,cancelled'],
            'media'          => ['nullable', 'array'],
            'media.*'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:10240'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $imagePath = $event->image;
        if ($request->hasFile('image')) {
            if ($event->image) FileHandle::fileDelete($event->image);
            $imagePath = FileHandle::fileUpload($request->file('image'), 'events/images');
            if (! $imagePath) {
                return $this->error('Image upload failed. Please try again.', [], 500);
            }
        }
        $owner_avatar = null;
        if ($request->hasFile('owner_avatar')) {
            $owner_avatar = FileHandle::fileUpload($request->file('owner_avatar'), 'events/images/owner_avatar/');
            if (! $owner_avatar) {
                return $this->error('Image upload failed. Please try again.', [], 500);
            }
        }

        $event->update([
            'owner_name'          => $request->owner_name,
            'owner_address'          => $request->owner_address,
            'owner_phone'          => $request->owner_phone,
            'owner_avatar'          => $owner_avatar,
            'title'          => $request->title,
            'description'    => $request->description,
            'address'        => $request->address,
            'city'           => $request->city,
            'state'          => $request->state,
            'zip_code'       => $request->zip_code,
            'country'        => $request->country ?? 'US',
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'website'        => $request->website,
            'image'          => $imagePath,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'entry_fee'      => $request->entry_fee,
            'capacity'       => $request->capacity,
            'eventable_type' => $request->eventable_type ?? $event->eventable_type,
            'eventable_id'   => $request->eventable_id   ?? $event->eventable_id,
            'status'         => $request->status          ?? $event->status,
        ]);

        // Multiple Media Upload
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $type = str_contains($file->getMimeType(), 'video') ? 'video' : 'image';
                $path = FileHandle::fileUpload($file, 'events/' . ($type === 'video' ? 'videos' : 'images'));

                if ($path) {
                    $event->media()->create([
                        'file_path'  => $path,
                        'file_name'  => $file->getClientOriginalName(),
                        'mime_type'  => $file->getMimeType(),
                        'file_size'  => $file->getSize(),
                        'media_type' => $type,
                    ]);
                }
            }
        }

        // Send notification to all users
        $users = User::all();
        Notification::send($users, new EventNotification($event, 'updated'));

        return $this->success('Event updated successfully.', [
            'event' => $event->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE  /admin/events/{event}
    |--------------------------------------------------------------------------
    */
    public function destroy(Event $event): JsonResponse
    {
        if ($event->image) FileHandle::fileDelete($event->image);

        // Send notification to all users
        $users = User::all();
        Notification::send($users, new EventNotification($event, 'deleted'));

        $event->delete();
        return $this->success('Event deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | PATCH  /admin/events/{event}/toggle-status
    |--------------------------------------------------------------------------
    */
    public function toggleStatus(Event $event): JsonResponse
    {
        $newStatus = $event->status === 'upcoming' ? 'ongoing' : 'upcoming';
        $event->update(['status' => $newStatus]);
        return $this->success('Event status updated to ' . ucfirst($newStatus) . '.', ['status' => $newStatus]);
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE  /admin/events/{event}/media/{mediaId}
    |--------------------------------------------------------------------------
    */
    public function removeMedia(Event $event, $mediaId): JsonResponse
    {
        $media = $event->media()->findOrFail($mediaId);

        if ($media->file_path) {
            FileHandle::fileDelete($media->file_path);
        }

        $media->delete();

        return $this->success('Media file removed successfully.');
    }
}
