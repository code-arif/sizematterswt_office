@extends('layout.master-layout')

@section('title', 'Event - ' . $event->title)

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Event Details</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
                                <li class="breadcrumb-item active">{{ $event->title }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- ── Left column ─────────────────────────────────────────────── --}}
                <div class="col-lg-8">
                    <div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Owner / Contact Person</h5>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <div class="flex-shrink-0">
                <img src="{{ $event->owner_avatar ? asset( $event->owner_avatar) : asset('admin/assets/images/users/user-dummy-img.jpg') }}"
                     alt="Owner Avatar"
                     class="rounded-circle avatar-lg img-thumbnail"
                     style="width: 70px; height: 70px; object-fit: cover;">
            </div>
            <div class="flex-grow-1">
                <h5 class="fs-15 mb-1">{{ $event->owner_name ?? 'Not Specified' }}</h5>
                <p class="text-muted mb-0"> Owner / Primary Contact</p>
            </div>
        </div>

        <hr class="text-muted opacity-25">

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-phone-fill text-primary fs-16"></i>
                    <div>
                        <small class="text-muted d-block">Owner Phone</small>
                        <span class="fw-medium">{{ $event->owner_phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-map-pin-user-fill text-success fs-16"></i>
                    <div>
                        <small class="text-muted d-block">Owner Address</small>
                        <span class="fw-medium">{{ $event->owner_address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                    {{-- Hero --}}
                    <div class="card overflow-hidden">
                        <div class="position-relative">
                            <img src="{{ $event->image ? asset('storage/' . $event->image) : asset('admin/assets/images/default/event-placeholder.jpg') }}"
                                alt="{{ $event->title }}" class="card-img-top"
                                style="height:280px;object-fit:cover;">
                            <div class="position-absolute top-0 end-0 p-3">
                                @php
                                    $statusMap = [
                                        'upcoming'  => 'bg-info',
                                        'ongoing'   => 'bg-success',
                                        'completed' => 'bg-secondary',
                                        'cancelled' => 'bg-danger',
                                    ];
                                @endphp
                                <span class="badge {{ $statusMap[$event->status] ?? 'bg-secondary' }} fs-12">
                                    {{ ucfirst($event->status) }}
                                </span>
                                @if($event->entry_fee == 0 || is_null($event->entry_fee))
                                    <span class="badge bg-success text-white fs-12 ms-1">
                                        <i class="ri-price-tag-3-line me-1"></i>Free
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">{{ $event->title }}</h4>
                                    <p class="text-muted mb-0"><i class="ri-map-pin-line me-1"></i>{{ $event->address }}</p>
                                </div>
                                <div class="flex-shrink-0 d-flex gap-2">
                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-primary">
                                        <i class="ri-pencil-fill me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-soft-danger" id="deleteEventBtn">
                                        <i class="ri-delete-bin-fill me-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                            @if($event->description)<hr><p class="text-muted mb-0">{{ $event->description }}</p>@endif
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Contact Information</h5></div>
                        <div class="card-body">
                            <div class="row g-3">

                                @if($event->phone)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-success text-success fs-18"><i class="ri-phone-line"></i></span>
                                        </div>
                                        <div><p class="mb-0 fw-medium">{{ $event->phone }}</p><small class="text-muted">Phone Number</small></div>
                                    </div>
                                </div>
                                @endif

                                @if($event->email)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-18"><i class="ri-mail-line"></i></span>
                                        </div>
                                        <div><p class="mb-0 fw-medium">{{ $event->email }}</p><small class="text-muted">Email Address</small></div>
                                    </div>
                                </div>
                                @endif

                                @if($event->website)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-info text-info fs-18"><i class="ri-global-line"></i></span>
                                        </div>
                                        <div>
                                            <a href="{{ $event->website }}" target="_blank" class="mb-0 fw-medium d-block">{{ $event->website }}</a>
                                            <small class="text-muted">Website</small>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-warning text-warning fs-18"><i class="ri-map-pin-2-line"></i></span>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-medium">{{ $event->city }}{{ $event->state ? ', ' . $event->state : '' }} {{ $event->zip_code }}</p>
                                            <small class="text-muted">{{ $event->country }}</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Map --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Location on Map</h5></div>
                        <div class="card-body p-0 overflow-hidden" style="border-radius:0 0 8px 8px;">
                            <div id="showMap" style="width:100%;height:380px;"></div>
                        </div>
                    </div>

                    {{-- Media Gallery --}}
                    @if($event->media->isNotEmpty())
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Gallery</h5>
                            <span class="badge bg-primary-subtle text-primary">{{ $event->media->count() }} files</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($event->media as $media)
                                <div class="col-lg-3 col-md-4 col-6">
                                    @if($media->media_type === 'video')
                                        <div class="position-relative rounded overflow-hidden"
                                            style="aspect-ratio:16/9;background:#000;cursor:pointer;"
                                            onclick="openMedia('{{ $media->url }}', 'video')">
                                            @if($media->thumbnail_path)
                                                <img src="{{ $media->thumbnail_url }}" class="w-100 h-100" style="object-fit:cover;opacity:.7;" alt="">
                                            @endif
                                            <div class="position-absolute top-50 start-50 translate-middle">
                                                <span class="avatar-sm bg-white rounded-circle d-flex align-items-center justify-content-center shadow">
                                                    <i class="ri-play-fill text-primary fs-18"></i>
                                                </span>
                                            </div>
                                            @if($media->duration_seconds)
                                                <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark fs-11">{{ gmdate('i:s', $media->duration_seconds) }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <img src="{{ $media->url }}" alt="{{ $media->caption ?? $event->title }}"
                                            class="img-fluid rounded w-100"
                                            style="aspect-ratio:16/9;object-fit:cover;cursor:pointer;"
                                            onclick="openMedia('{{ $media->url }}', 'image')">
                                    @endif
                                    @if($media->caption)<small class="text-muted d-block mt-1 text-truncate">{{ $media->caption }}</small>@endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
                {{-- /left col --}}

                {{-- ── Right column ─────────────────────────────────────────────── --}}
                <div class="col-lg-4">

                    {{-- Overview --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Overview</h5></div>
                        <div class="card-body">

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Added By</span>
                                <span class="fw-medium">{{ $event->admin?->name ?? '—' }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Start Date</span>
                                <span class="fw-medium">
                                    {{ $event->start_date?->format('d M Y, h:i A') ?? '—' }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">End Date</span>
                                <span class="fw-medium">
                                    {{ $event->end_date?->format('d M Y, h:i A') ?? '—' }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Entry Fee</span>
                                <span class="fw-medium">
                                    @if($event->entry_fee > 0)
                                        ${{ number_format($event->entry_fee, 2) }}
                                    @else
                                        <span class="badge bg-success-subtle text-success">Free</span>
                                    @endif
                                </span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Capacity</span>
                                <span class="fw-medium">
                                    {{ $event->capacity ? number_format($event->capacity) . ' attendees' : '—' }}
                                </span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Created</span>
                                <span class="fw-medium">{{ $event->created_at->format('d M Y') }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Last Updated</span>
                                <span class="fw-medium">{{ $event->updated_at->format('d M Y') }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Total Visitors</span>
                                <span class="badge bg-primary-subtle text-primary fw-medium">{{ $event->visits->count() }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Media Files</span>
                                <span class="badge bg-info-subtle text-info fw-medium">{{ $event->media->count() }}</span>
                            </div>

                            <div class="d-flex align-items-center justify-content-between py-2">
                                <span class="text-muted">Status</span>
                                @php
                                    $sc = [
                                        'upcoming'  => 'info',
                                        'ongoing'   => 'success',
                                        'completed' => 'secondary',
                                        'cancelled' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $sc[$event->status] ?? 'secondary' }}-subtle text-{{ $sc[$event->status] ?? 'secondary' }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </div>

                        </div>
                    </div>

                    {{-- Coordinates --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Coordinates</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1">Latitude</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control bg-light" value="{{ $event->latitude }}" readonly>
                                        <button class="btn btn-light" type="button"
                                            onclick="navigator.clipboard.writeText('{{ $event->latitude }}'); Toast.success('Copied!')">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1">Longitude</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control bg-light" value="{{ $event->longitude }}" readonly>
                                        <button class="btn btn-light" type="button"
                                            onclick="navigator.clipboard.writeText('{{ $event->longitude }}'); Toast.success('Copied!')">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <a href="https://maps.google.com/?q={{ $event->latitude }},{{ $event->longitude }}"
                                        target="_blank" class="btn btn-sm btn-soft-info w-100">
                                        <i class="ri-google-line me-1"></i> Open in Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Quick Actions</h5></div>
                        <div class="card-body d-grid gap-2">
                            <button type="button"
                                class="btn btn-soft-{{ $event->status === 'upcoming' ? 'success' : 'info' }}"
                                id="toggleStatusBtn">
                                <i class="ri-toggle-line me-1"></i>
                                {{ $event->status === 'upcoming' ? 'Set Ongoing' : 'Set Upcoming' }}
                            </button>
                            <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-primary">
                                <i class="ri-pencil-fill me-1"></i> Edit Event
                            </a>
                        </div>
                    </div>

                </div>
                {{-- /right col --}}

            </div>

        </div>
    </div>

    {{-- Lightbox --}}
    <div class="modal fade" id="mediaModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark border-0">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-2" id="mediaModalBody"></div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    window.__GOOGLE_MAPS_KEY = '{{ env('GOOGLE_MAPS_API_KEY') }}';
    window.__EVENT_LAT       = {{ $event->latitude }};
    window.__EVENT_LNG       = {{ $event->longitude }};
    window.__EVENT_TITLE     = @json($event->title);
    window.__EVENT_ID        = {{ $event->id }};
</script>
<script>
(function () {
    const s = document.createElement('script');
    s.src = `https://maps.googleapis.com/maps/api/js?key=${window.__GOOGLE_MAPS_KEY}&callback=initShowMap`;
    s.async = true; s.defer = true;
    document.head.appendChild(s);
})();

window.initShowMap = function () {
    const pos = { lat: window.__EVENT_LAT, lng: window.__EVENT_LNG };
    const map = new google.maps.Map(document.getElementById('showMap'), {
        zoom: 14, center: pos, mapTypeId: 'roadmap', streetViewControl: false, mapTypeControl: false,
    });
    const marker = new google.maps.Marker({
        position: pos, map, title: window.__EVENT_TITLE,
    });
    const infoWindow = new google.maps.InfoWindow({
        content: `<strong>${window.__EVENT_TITLE}</strong><br><small>${window.__EVENT_LAT.toFixed(6)}, ${window.__EVENT_LNG.toFixed(6)}</small>`
    });
    marker.addListener('click', () => infoWindow.open(map, marker));
};
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const eventId = window.__EVENT_ID;

    /* ── Toggle status ──────────────────────────────────────────────────── */
    document.getElementById('toggleStatusBtn').addEventListener('click', function () {
        axios.patch(`/events/${eventId}/toggle-status`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        })
        .then(res => { Toast.success(res.data.message); setTimeout(() => location.reload(), 800); })
        .catch(err => Toast.fromResponse(err.response?.data));
    });

    /* ── Delete ─────────────────────────────────────────────────────────── */
    document.getElementById('deleteEventBtn').addEventListener('click', function () {
        Alert.confirm('This event will be permanently removed.', {
            title: 'Delete Event?', type: 'danger', confirmText: 'Yes, delete it',
        }).then(confirmed => {
            if (!confirmed) return;
            axios.delete(`/admin/events/${eventId}`, {
                data: { _token: document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(res => {
                Toast.success(res.data.message);
                setTimeout(() => window.location.href = '{{ route('admin.events.index') }}', 800);
            })
            .catch(err => Toast.fromResponse(err.response?.data));
        });
    });

    /* ── Lightbox ───────────────────────────────────────────────────────── */
    window.openMedia = function (url, type) {
        const body = document.getElementById('mediaModalBody');
        body.innerHTML = type === 'video'
            ? `<video src="${url}" controls autoplay class="w-100 rounded" style="max-height:70vh;"></video>`
            : `<img src="${url}" class="img-fluid rounded" style="max-height:80vh;" alt="">`;
        new bootstrap.Modal(document.getElementById('mediaModal')).show();
    };

    document.getElementById('mediaModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('mediaModalBody').innerHTML = '';
    });

});
</script>
@endpush
