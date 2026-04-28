@extends('layout.master-layout')
@section('title', 'Advertisement Detail')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Advertisement Detail</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.advertisements.index') }}">Advertisements</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            {{-- Left: Banner + Stats --}}
            <div class="col-xl-4">
                {{-- Banner Preview --}}
                <div class="card">
                    <div class="card-body text-center p-3">
                        @if($advertisement->media_type === 'video')
                            <video src="{{ asset('storage/' . $advertisement->image) }}"
                                class="rounded w-100" style="max-height:180px;object-fit:cover;" controls autoplay muted loop></video>
                        @else
                            <img src="{{ asset('storage/' . $advertisement->image) }}"
                                class="rounded w-100" style="max-height:180px;object-fit:cover;" />
                        @endif

                        <div class="mt-3">
                            @if($advertisement->is_expired)
                                <span class="badge bg-danger">Expired</span>
                            @elseif($advertisement->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>

                        <h5 class="mt-3 mb-1">{{ $advertisement->title }}</h5>
                        @if($advertisement->subtitle)
                            <p class="text-muted fs-13">{{ $advertisement->subtitle }}</p>
                        @endif

                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('admin.advertisements.edit', $advertisement->id) }}"
                                class="btn btn-primary btn-sm">
                                <i class="ri-pencil-fill me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-soft-danger btn-sm" id="toggleStatusBtn"
                                data-id="{{ $advertisement->id }}"
                                data-status="{{ $advertisement->status }}">
                                <i class="ri-toggle-line me-1"></i>
                                {{ $advertisement->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Impression Stats --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Impression Stats</h5></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm bg-info-subtle rounded flex-shrink-0 me-3">
                                <i class="bx bx-show text-info fs-24 d-flex align-items-center justify-content-center h-100"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $totalImpressions }}</h4>
                                <span class="text-muted fs-13">Total Impressions</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-warning-subtle rounded flex-shrink-0 me-3">
                                <i class="bx bx-x-circle text-warning fs-24 d-flex align-items-center justify-content-center h-100"></i>
                            </div>
                            <div>
                                <h4 class="mb-0">{{ $dismissed }}</h4>
                                <span class="text-muted fs-13">Dismissed</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Details + Map --}}
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Ad Details</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr>
                                        <th class="text-muted" style="width:180px;">CTA Label</th>
                                        <td>{{ $advertisement->cta_label }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Linked To</th>
                                        <td>
                                            @if($advertisement->advertiseable)
                                                @php
                                                    $type  = class_basename($advertisement->advertiseable_type);
                                                    $name  = $advertisement->advertiseable->name ?? $advertisement->advertiseable->title ?? '—';
                                                    $color = match($type) { 'Farm'=>'success','Ranch'=>'warning','Event'=>'danger', default=>'secondary' };
                                                @endphp
                                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">{{ $type }}</span>
                                                <span class="ms-1">{{ $name }}</span>
                                            @else
                                                <span class="text-muted">Standalone ad</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Trigger Radius</th>
                                        <td>
                                            {{ $advertisement->radius_meters >= 1000
                                                ? round($advertisement->radius_meters / 1000, 1).' km'
                                                : $advertisement->radius_meters.' m' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Trigger Coordinates</th>
                                        <td>{{ $advertisement->trigger_latitude }}, {{ $advertisement->trigger_longitude }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Schedule</th>
                                        <td>
                                            {{ $advertisement->starts_at?->format('d M Y') ?? 'Always' }}
                                            –
                                            {{ $advertisement->ends_at?->format('d M Y') ?? '∞' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Created By</th>
                                        <td>{{ $advertisement->admin?->profile?->name ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Created At</th>
                                        <td>{{ $advertisement->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Trigger Map (read-only) --}}
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Trigger Location & Radius</h5></div>
                    <div class="card-body p-0">
                        <div id="showMap" style="height:360px;border-radius:0 0 8px 8px;"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initShowMap" async defer></script>
<script>
function initShowMap() {
    var lat = {{ $advertisement->trigger_latitude }};
    var lng = {{ $advertisement->trigger_longitude }};
    var r   = {{ $advertisement->radius_meters }};

    var map = new google.maps.Map(document.getElementById('showMap'), {
        center: { lat: lat, lng: lng },
        zoom: 13,
        gestureHandling: 'cooperative',
    });

    new google.maps.Marker({ position: { lat: lat, lng: lng }, map: map });

    new google.maps.Circle({
        map: map,
        center: { lat: lat, lng: lng },
        radius: r,
        fillColor: '#405189',
        fillOpacity: 0.15,
        strokeColor: '#405189',
        strokeOpacity: 0.6,
        strokeWeight: 1,
    });
}

$(function () {
    $('#toggleStatusBtn').on('click', function () {
        var btn = $(this);
        $.ajax({
            url: '/admin/advertisements/' + btn.data('id') + '/toggle-status',
            method: 'PATCH',
            data: { _token: '{{ csrf_token() }}' },
            success: function (res) {
                toastr.success(res.message);
                setTimeout(function () { location.reload(); }, 800);
            }
        });
    });
});
</script>
@endpush
