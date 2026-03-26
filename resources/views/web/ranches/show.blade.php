@extends('layout.master-layout')

@section('title', 'Ranch - ' . $ranch->name)

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Ranch Details</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.ranches.index') }}">Ranches</a></li>
                                <li class="breadcrumb-item active">{{ $ranch->name }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                {{-- ── Left column ─────────────────────────────────────────────── --}}
                <div class="col-lg-8">


                    {{-- Owner Information --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Owner / Contact Person</h5>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <div class="flex-shrink-0">
                <img src="{{ $ranch->owner_avatar ? asset( $ranch->owner_avatar) : asset('admin/assets/images/users/user-dummy-img.jpg') }}"
                     alt="Owner Avatar"
                     class="rounded-circle avatar-lg img-thumbnail"
                     style="width: 70px; height: 70px; object-fit: cover;">
            </div>
            <div class="flex-grow-1">
                <h5 class="fs-15 mb-1">{{ $ranch->owner_name ?? 'Not Specified' }}</h5>
                <p class="text-muted mb-0">Ranch Owner / Primary Contact</p>
            </div>
        </div>

        <hr class="text-muted opacity-25">

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-phone-fill text-primary fs-16"></i>
                    <div>
                        <small class="text-muted d-block">Owner Phone</small>
                        <span class="fw-medium">{{ $ranch->owner_phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-map-pin-user-fill text-success fs-16"></i>
                    <div>
                        <small class="text-muted d-block">Owner Address</small>
                        <span class="fw-medium">{{ $ranch->owner_address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                    {{-- Hero --}}
                    <div class="card overflow-hidden">
                        <div class="position-relative">
                            <img src="{{ $ranch->thumbnail ? asset('storage/' . $ranch->thumbnail) : asset('admin/assets/images/default/ranch-placeholder.jpg') }}"
                                alt="{{ $ranch->name }}" class="card-img-top"
                                style="height:280px;object-fit:cover;">
                            <div class="position-absolute top-0 end-0 p-3">
                                @php $statusMap = ['active'=>'bg-success','inactive'=>'bg-secondary','pending'=>'bg-warning']; @endphp
                                <span class="badge {{ $statusMap[$ranch->status] ?? 'bg-secondary' }} fs-12">{{ ucfirst($ranch->status) }}</span>
                                @if($ranch->is_featured)
                                    <span class="badge bg-warning text-dark fs-12 ms-1"><i class="ri-star-fill me-1"></i>Featured</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <h4 class="mb-1">{{ $ranch->name }}</h4>
                                    <p class="text-muted mb-0"><i class="ri-map-pin-line me-1"></i>{{ $ranch->address }}</p>
                                </div>
                                <div class="flex-shrink-0 d-flex gap-2">
                                    <a href="{{ route('admin.ranches.edit', $ranch->id) }}" class="btn btn-sm btn-primary">
                                        <i class="ri-pencil-fill me-1"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-soft-danger" id="deleteRanchBtn">
                                        <i class="ri-delete-bin-fill me-1"></i> Delete
                                    </button>
                                </div>
                            </div>
                            @if($ranch->description)<hr><p class="text-muted mb-0">{{ $ranch->description }}</p>@endif
                        </div>
                    </div>

                    {{-- Contact --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Contact Information</h5></div>
                        <div class="card-body">
                            <div class="row g-3">

                                @if($ranch->phone)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-success text-success fs-18"><i class="ri-phone-line"></i></span>
                                        </div>
                                        <div><p class="mb-0 fw-medium">{{ $ranch->phone }}</p><small class="text-muted">Phone Number</small></div>
                                    </div>
                                </div>
                                @endif

                                @if($ranch->email)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-primary text-primary fs-18"><i class="ri-mail-line"></i></span>
                                        </div>
                                        <div><p class="mb-0 fw-medium">{{ $ranch->email }}</p><small class="text-muted">Email Address</small></div>
                                    </div>
                                </div>
                                @endif

                                @if($ranch->website)
                                <div class="col-lg-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            <span class="avatar-title rounded-circle bg-soft-info text-info fs-18"><i class="ri-global-line"></i></span>
                                        </div>
                                        <div>
                                            <a href="{{ $ranch->website }}" target="_blank" class="mb-0 fw-medium d-block">{{ $ranch->website }}</a>
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
                                            <p class="mb-0 fw-medium">{{ $ranch->city }}{{ $ranch->state ? ', ' . $ranch->state : '' }} {{ $ranch->zip_code }}</p>
                                            <small class="text-muted">{{ $ranch->country }}</small>
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
                    @if($ranch->media->isNotEmpty())
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">Gallery</h5>
                            <span class="badge bg-primary-subtle text-primary">{{ $ranch->media->count() }} files</span>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($ranch->media as $media)
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
                                        <img src="{{ $media->url }}" alt="{{ $media->caption ?? $ranch->name }}"
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
                                <span class="fw-medium">{{ $ranch->admin?->name ?? '—' }}</span>
                            </div>
                            {{-- Ranch-specific: acreage --}}
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Acreage</span>
                                <span class="fw-medium">
                                    @if($ranch->acreage)
                                        {{ number_format($ranch->acreage, 2) }} acres
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Created</span>
                                <span class="fw-medium">{{ $ranch->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Last Updated</span>
                                <span class="fw-medium">{{ $ranch->updated_at->format('d M Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Total Visitors</span>
                                <span class="badge bg-primary-subtle text-primary fw-medium">{{ $ranch->visits->count() }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="text-muted">Media Files</span>
                                <span class="badge bg-info-subtle text-info fw-medium">{{ $ranch->media->count() }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <span class="text-muted">Status</span>
                                @php $sc = ['active'=>'success','inactive'=>'secondary','pending'=>'warning']; @endphp
                                <span class="badge bg-{{ $sc[$ranch->status] ?? 'secondary' }}-subtle text-{{ $sc[$ranch->status] ?? 'secondary' }}">
                                    {{ ucfirst($ranch->status) }}
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
                                        <input type="text" class="form-control bg-light" value="{{ $ranch->latitude }}" readonly>
                                        <button class="btn btn-light" type="button"
                                            onclick="navigator.clipboard.writeText('{{ $ranch->latitude }}'); Toast.success('Copied!')">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1">Longitude</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control bg-light" value="{{ $ranch->longitude }}" readonly>
                                        <button class="btn btn-light" type="button"
                                            onclick="navigator.clipboard.writeText('{{ $ranch->longitude }}'); Toast.success('Copied!')">
                                            <i class="ri-file-copy-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <a href="https://maps.google.com/?q={{ $ranch->latitude }},{{ $ranch->longitude }}"
                                        target="_blank" class="btn btn-sm btn-soft-info w-100">
                                        <i class="ri-google-line me-1"></i> Open in Google Maps
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Marker --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Map Marker</h5></div>
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="p-3 rounded" style="background:{{ $ranch->marker_color }};">
                                    <i class="ri-map-pin-fill text-white fs-24"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fw-medium">{{ $ranch->marker_icon ?? 'ranch_pin' }}</p>
                                    <small class="text-muted font-monospace">{{ $ranch->marker_color }}</small>
                                </div>
                            </div>
                            <div class="p-3 rounded bg-light">
                                <p class="mb-1 small text-muted fw-semibold text-uppercase" style="letter-spacing:.5px;">Flutter Usage</p>
                                <code class="small">
                                    color: Color(0xFF{{ ltrim($ranch->marker_color, '#') }})<br>
                                    icon: "{{ $ranch->marker_icon ?? 'ranch_pin' }}"
                                </code>
                            </div>
                        </div>
                    </div>

                    {{-- Tags --}}
                    @if($ranch->tags && count($ranch->tags))
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Tags</h5></div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($ranch->tags as $tag)
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
                            <button type="button" class="btn btn-soft-{{ $ranch->status === 'active' ? 'warning' : 'success' }}" id="toggleStatusBtn">
                                <i class="ri-toggle-line me-1"></i>
                                {{ $ranch->status === 'active' ? 'Set Inactive' : 'Set Active' }}
                            </button>
                            <button type="button" class="btn btn-soft-{{ $ranch->is_featured ? 'secondary' : 'warning' }}" id="toggleFeaturedBtn">
                                <i class="ri-star-{{ $ranch->is_featured ? 'line' : 'fill' }} me-1"></i>
                                {{ $ranch->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}
                            </button>
                            <a href="{{ route('admin.ranches.edit', $ranch->id) }}" class="btn btn-primary">
                                <i class="ri-pencil-fill me-1"></i> Edit Ranch
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
    window.__GOOGLE_MAPS_KEY    = '{{ env('GOOGLE_MAPS_API_KEY') }}';
    window.__RANCH_LAT          = {{ $ranch->latitude }};
    window.__RANCH_LNG          = {{ $ranch->longitude }};
    window.__RANCH_NAME         = @json($ranch->name);
    window.__RANCH_MARKER_COLOR = '{{ $ranch->marker_color }}';
    window.__RANCH_ID           = {{ $ranch->id }};
</script>
<script>
(function() {
    const s = document.createElement('script');
    s.src = `https://maps.googleapis.com/maps/api/js?key=${window.__GOOGLE_MAPS_KEY}&callback=initShowMap`;
    s.async = true; s.defer = true;
    document.head.appendChild(s);
})();

window.initShowMap = function () {
    const pos = { lat: window.__RANCH_LAT, lng: window.__RANCH_LNG };
    const map = new google.maps.Map(document.getElementById('showMap'), {
        zoom: 14, center: pos, mapTypeId: 'roadmap', streetViewControl: false, mapTypeControl: false,
    });
    const marker = new google.maps.Marker({
        position: pos, map, title: window.__RANCH_NAME,
        icon: { path: google.maps.SymbolPath.CIRCLE, scale: 12, fillColor: window.__RANCH_MARKER_COLOR, fillOpacity: 1, strokeColor: '#ffffff', strokeWeight: 2.5 },
    });
    const infoWindow = new google.maps.InfoWindow({
        content: `<strong>${window.__RANCH_NAME}</strong><br><small>${window.__RANCH_LAT.toFixed(6)}, ${window.__RANCH_LNG.toFixed(6)}</small>`
    });
    marker.addListener('click', () => infoWindow.open(map, marker));
};
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const ranchId = window.__RANCH_ID;

    document.getElementById('toggleStatusBtn').addEventListener('click', function () {
        axios.patch(`/admin/ranches/${ranchId}/toggle-status`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        })
        .then(res => { Toast.success(res.data.message); setTimeout(() => location.reload(), 800); })
        .catch(err => Toast.fromResponse(err.response?.data));
    });

    document.getElementById('toggleFeaturedBtn').addEventListener('click', function () {
        axios.patch(`/admin/ranches/${ranchId}/toggle-featured`, {
            _token: document.querySelector('meta[name="csrf-token"]').content
        })
        .then(res => { Toast.success(res.data.message); setTimeout(() => location.reload(), 800); })
        .catch(err => Toast.fromResponse(err.response?.data));
    });

    document.getElementById('deleteRanchBtn').addEventListener('click', function () {
        Alert.confirm('This ranch will be permanently removed.', {
            title: 'Delete Ranch?', type: 'danger', confirmText: 'Yes, delete it',
        }).then(confirmed => {
            if (!confirmed) return;
            axios.delete(`/admin/ranches/${ranchId}`, {
                data: { _token: document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(res => {
                Toast.success(res.data.message);
                setTimeout(() => window.location.href = '{{ route('admin.ranches.index') }}', 800);
            })
            .catch(err => Toast.fromResponse(err.response?.data));
        });
    });

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
