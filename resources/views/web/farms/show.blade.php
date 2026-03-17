@extends('layout.master-layout')

@section('title', 'Farm - ' . $farm->name)

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Page Title ──────────────────────────────────────────────────── --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Farm Details</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.farms.index') }}">Farms</a></li>
                                <li class="breadcrumb-item active">{{ $farm->name }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- ── Left column ─────────────────────────────────────────────── --}}
                <div class="col-lg-8">

                    {{-- Hero / Thumbnail --}}
                    <div class="card overflow-hidden">
                        <div class="position-relative">
                            <img src="{{ $farm->thumbnail ? asset('storage/' . $farm->thumbnail) : asset('admin/assets/images/default/farm-placeholder.jpg') }}"
                                alt="{{ $farm->name }}"
                                class="card-img-top"
                                style="height:280px;object-fit:cover;">

                            {{-- Status badge overlay --}}
                            <div class="position-absolute top-0 end-0 p-3">
                                @php
                                    $statusMap = [
                                        'active'   => 'bg-success',
                                        'inactive' => 'bg-secondary',
                                        'pending'  => 'bg-warning',
                                    ];
                                @endphp
                                <span class="badge {{ $statusMap[$farm->status] ?? 'bg-secondary' }} fs-12">
                                    {{ ucfirst($farm->status) }}
                                </span>
                                @if($farm->is_featured)
                                    <span class="badge bg-warning text-dark fs-12 ms-1">
                                        <i class="ri-star-fill me-1"></i> Featured
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">{{ $farm->name }}</h4>
                                    <p class="text-muted mb-0">
                                        <i class="ri-map-pin-line me-1"></i>{{ $farm->address }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 d-flex gap-2">
                                    <a href="{{ route('admin.farms.edit', $farm->id) }}" class="btn btn-sm btn-primary">
                                        <i class="ri-pencil-fill me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-soft-danger" id="deleteFarmBtn">
                                        <i class="ri-delete-bin-fill me-1"></i> Delete
                                    </button>
                                </div>
                            </div>

                            @if($farm->description)
                                <hr>
                                <p class="text-muted mb-0">{{ $farm->description }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Contact & Details --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Contact Information</h5></div>
                        <div class="card-body">
                            <div class="row g-3">

                                @if($farm->phone)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-success text-success fs-18">
                                                <i class="ri-phone-line"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-medium">{{ $farm->phone }}</p>
                                            <small class="text-muted">Phone Number</small>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($farm->email)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-18">
                                                <i class="ri-mail-line"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-medium">{{ $farm->email }}</p>
                                            <small class="text-muted">Email Address</small>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($farm->website)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-info text-info fs-18">
                                                <i class="ri-global-line"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ $farm->website }}" target="_blank" class="mb-0 fw-medium d-block">
                                                {{ $farm->website }}
                                            </a>
                                            <small class="text-muted">Website</small>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-warning text-warning fs-18">
                                                <i class="ri-map-pin-2-line"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-medium">{{ $farm->city }}{{ $farm->state ? ', ' . $farm->state : '' }} {{ $farm->zip_code }}</p>
                                            <small class="text-muted">{{ $farm->country }}</small>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Map preview --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Location on Map</h5></div>
                        <div class="card-body p-0 overflow-hidden" style="border-radius:0 0 8px 8px;">
                            <div id="showMap" style="width:100%;height:380px;"></div>
                        </div>
                    </div>

                    {{-- Media Gallery --}}
                    @if($farm->media->isNotEmpty())
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Gallery</h5>
                            <span class="badge bg-primary-subtle text-primary">{{ $farm->media->count() }} files</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($farm->media as $media)
                                <div class="col-lg-3 col-md-4 col-6">
                                    @if($media->media_type === 'video')
                                        <div class="position-relative rounded overflow-hidden"
                                            style="aspect-ratio:16/9;background:#000;cursor:pointer;"
                                            onclick="openMedia('{{ $media->url }}', 'video')">
                                            @if($media->thumbnail_path)
                                                <img src="{{ $media->thumbnail_url }}" class="w-100 h-100"
                                                    style="object-fit:cover;opacity:.7;" alt="">
                                            @endif
                                            <div class="position-absolute top-50 start-50 translate-middle">
                                                <span class="avatar-sm bg-white rounded-circle d-flex align-items-center justify-content-center shadow">
                                                    <i class="ri-play-fill text-primary fs-18"></i>
                                                </span>
                                            </div>
                                            @if($media->duration_seconds)
                                                <span class="position-absolute bottom-0 end-0 m-2 badge bg-dark fs-11">
                                                    {{ gmdate('i:s', $media->duration_seconds) }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <img src="{{ $media->url }}" alt="{{ $media->caption ?? $farm->name }}"
                                            class="img-fluid rounded w-100"
                                            style="aspect-ratio:16/9;object-fit:cover;cursor:pointer;"
                                            onclick="openMedia('{{ $media->url }}', 'image')">
                                    @endif
                                    @if($media->caption)
                                        <small class="text-muted d-block mt-1 text-truncate">{{ $media->caption }}</small>
                                    @endif
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

                    {{-- Quick Stats --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Overview</h5></div>
                        <div class="card-body">

                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Added By</span>
                                <span class="fw-medium">{{ $farm->admin?->profile?->name ?? '—' }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Created</span>
                                <span class="fw-medium">{{ $farm->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Last Updated</span>
                                <span class="fw-medium">{{ $farm->updated_at->format('d M Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Total Visitors</span>
                                <span class="badge bg-primary-subtle text-primary fw-medium">
                                    {{ $farm->visits->count() }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Media Files</span>
                                <span class="badge bg-info-subtle text-info fw-medium">
                                    {{ $farm->media->count() }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <span class="text-muted">Status</span>
                                @php $sc = ['active'=>'success','inactive'=>'secondary','pending'=>'warning']; @endphp
                                <span class="badge bg-{{ $sc[$farm->status] ?? 'secondary' }}-subtle text-{{ $sc[$farm->status] ?? 'secondary' }}">
                                    {{ ucfirst($farm->status) }}
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
                                        <input type="text" class="form-control bg-light" value="{{ $farm->latitude }}" readonly>
                                        <button class="btn btn-light" type="button"
                                            onclick="navigator.clipboard.writeText('{{ $farm->latitude }}'); Toast.success('Copied!')"
                                            title="Copy">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1">Longitude</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control bg-light" value="{{ $farm->longitude }}" readonly>
                                        <button class="btn btn-light" type="button"
                                            onclick="navigator.clipboard.writeText('{{ $farm->longitude }}'); Toast.success('Copied!')"
                                            title="Copy">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <a href="https://maps.google.com/?q={{ $farm->latitude }},{{ $farm->longitude }}"
                                        target="_blank" class="btn btn-sm btn-soft-info w-100">
                                        <i class="ri-google-line me-1"></i> Open in Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Marker Info --}}
                    <div class="card d-none">
                        <div class="card-header"><h5 class="card-title mb-0">Map Marker</h5></div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="p-3 rounded" style="background:{{ $farm->marker_color }};">
                                    <i class="ri-map-pin-fill text-white fs-24"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">{{ $farm->marker_icon }}</p>
                                    <small class="text-muted font-monospace">{{ $farm->marker_color }}</small>
                                </div>
                            </div>
                            <div class="p-3 rounded bg-light">
                                <p class="mb-1 small text-muted fw-semibold text-uppercase" style="letter-spacing:.5px;">
                                    Flutter Usage
                                </p>
                                <code class="small">
                                    color: Color(0xFF{{ ltrim($farm->marker_color, '#') }})<br>
                                    icon: "{{ $farm->marker_icon }}"
                                </code>
                            </div>
                        </div>
                    </div>

                    {{-- Tags --}}
                    @if($farm->tags && count($farm->tags))
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Tags</h5></div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($farm->tags as $tag)
                                    <span class="badge bg-primary-subtle text-primary fs-12">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Quick Actions --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Quick Actions</h5></div>
                        <div class="card-body d-grid gap-2">
                            <button type="button" class="btn btn-soft-{{ $farm->status === 'active' ? 'warning' : 'success' }}"
                                id="toggleStatusBtn">
                                <i class="ri-toggle-line me-1"></i>
                                {{ $farm->status === 'active' ? 'Set Inactive' : 'Set Active' }}
                            </button>
                            <button type="button" class="d-none btn btn-soft-{{ $farm->is_featured ? 'secondary' : 'warning' }}"
                                id="toggleFeaturedBtn">
                                <i class="ri-star-{{ $farm->is_featured ? 'line' : 'fill' }} me-1"></i>
                                {{ $farm->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}
                            </button>
                            <a href="{{ route('admin.farms.edit', $farm->id) }}" class="btn btn-primary">
                                <i class="ri-pencil-fill me-1"></i> Edit Farm
                            </a>
                        </div>
                    </div>

                </div>
                {{-- /right col --}}

            </div>

        </div>
    </div>

    {{-- ── Lightbox Modal ───────────────────────────────────────────────────── --}}
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
    window.__GOOGLE_MAPS_KEY   = '{{ env('GOOGLE_MAPS_API_KEY') }}';
    window.__FARM_LAT          = {{ $farm->latitude }};
    window.__FARM_LNG          = {{ $farm->longitude }};
    window.__FARM_NAME         = @json($farm->name);
    window.__FARM_MARKER_COLOR = '{{ $farm->marker_color }}';
    window.__FARM_ID           = {{ $farm->id }};
</script>
<script>
/* ── Google Map (read-only) ─────────────────────────────────────────────── */
(function() {
    const s   = document.createElement('script');
    s.src     = `https://maps.googleapis.com/maps/api/js?key=${window.__GOOGLE_MAPS_KEY}&callback=initShowMap`;
    s.async   = true;
    s.defer   = true;
    document.head.appendChild(s);
})();

window.initShowMap = function () {
    const pos = { lat: window.__FARM_LAT, lng: window.__FARM_LNG };

    const map = new google.maps.Map(document.getElementById('showMap'), {
        zoom              : 14,
        center            : pos,
        mapTypeId         : 'roadmap',
        streetViewControl : false,
        zoomControl       : true,
        mapTypeControl    : false,
    });

    const marker = new google.maps.Marker({
        position  : pos,
        map       : map,
        title     : window.__FARM_NAME,
        icon: {
            path        : google.maps.SymbolPath.CIRCLE,
            scale       : 12,
            fillColor   : window.__FARM_MARKER_COLOR,
            fillOpacity : 1,
            strokeColor : '#ffffff',
            strokeWeight: 2.5,
        },
    });

    const infoWindow = new google.maps.InfoWindow({
        content: `<div style="font-family:inherit;">
            <strong>${window.__FARM_NAME}</strong><br>
            <small class="text-muted">${window.__FARM_LAT.toFixed(6)}, ${window.__FARM_LNG.toFixed(6)}</small>
        </div>`
    });

    marker.addListener('click', () => infoWindow.open(map, marker));
};
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const farmId = window.__FARM_ID;

    /* ── Toggle Status ──────────────────────────────────────────────────── */
    document.getElementById('toggleStatusBtn').addEventListener('click', function () {
        axios.patch(`/farms/${farmId}/toggle-status`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        })
        .then(res => {
            Toast.success(res.data.message);
            setTimeout(() => location.reload(), 800);
        })
        .catch(err => Toast.fromResponse(err.response?.data));
    });

    /* ── Toggle Featured ────────────────────────────────────────────────── */
    document.getElementById('toggleFeaturedBtn').addEventListener('click', function () {
        axios.patch(`/farms/${farmId}/toggle-featured`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        })
        .then(res => {
            Toast.success(res.data.message);
            setTimeout(() => location.reload(), 800);
        })
        .catch(err => Toast.fromResponse(err.response?.data));
    });

    /* ── Delete ─────────────────────────────────────────────────────────── */
    document.getElementById('deleteFarmBtn').addEventListener('click', function () {
        Alert.confirm('This farm will be permanently removed.', {
            title       : 'Delete Farm?',
            type        : 'danger',
            confirmText : 'Yes, delete it',
        }).then(confirmed => {
            if (!confirmed) return;
            axios.delete(`/admin/farms/${farmId}`, {
                data: { _token: document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(res => {
                Toast.success(res.data.message);
                setTimeout(() => window.location.href = '{{ route('admin.farms.index') }}', 800);
            })
            .catch(err => Toast.fromResponse(err.response?.data));
        });
    });

    /* ── Lightbox ───────────────────────────────────────────────────────── */
    window.openMedia = function (url, type) {
        const body = document.getElementById('mediaModalBody');
        if (type === 'video') {
            body.innerHTML = `<video src="${url}" controls autoplay
                class="w-100 rounded" style="max-height:70vh;"></video>`;
        } else {
            body.innerHTML = `<img src="${url}" class="img-fluid rounded" style="max-height:80vh;" alt="">`;
        }
        new bootstrap.Modal(document.getElementById('mediaModal')).show();
    };

    // Clear video on modal close (stop autoplay)
    document.getElementById('mediaModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('mediaModalBody').innerHTML = '';
    });

});
</script>
@endpush
