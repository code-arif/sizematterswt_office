{{-- ══════════════════════════════════════════════════════════
     resources/views/web/advertisements/edit.blade.php
══════════════════════════════════════════════════════════ --}}
@extends('layout.master-layout')
@section('title', 'Edit Advertisement')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Edit Advertisement</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.advertisements.index') }}">Advertisements</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form id="adForm" enctype="multipart/form-data" method="POST">
            @csrf
            <input type="hidden" name="_method" value="POST">

            <div class="row">

                {{-- Left Column --}}
                <div class="col-xl-8">

                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Ad Information</h5></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ $advertisement->title }}" maxlength="150">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control"
                                    value="{{ $advertisement->subtitle }}" maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">CTA Button Label</label>
                                <input type="text" name="cta_label" class="form-control"
                                    value="{{ $advertisement->cta_label }}" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Banner Image</h5></div>
                        <div class="card-body">
                            {{-- Current image --}}
                            <div class="mb-3">
                                <p class="text-muted fs-13 mb-1">Current Banner:</p>
                                <img src="{{ asset('storage/' . $advertisement->image) }}"
                                    id="imagePreview"
                                    class="rounded border mb-2"
                                    style="max-width:100%;height:160px;object-fit:cover;" />
                            </div>
                            <div class="mb-1">
                                <label class="form-label">Replace Image (optional)</label>
                                <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                                <div class="form-text">Leave blank to keep current image.</div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Trigger Location <span class="text-danger">*</span></h5>
                            <small class="text-muted">Click map to move the trigger point</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" id="locationSearch" class="form-control"
                                        placeholder="Search address...">
                                    <button class="btn btn-outline-primary" type="button" id="searchBtn">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </div>

                            <div id="triggerMap" style="height:380px;border-radius:8px;border:1px solid #dee2e6;"></div>

                            <input type="hidden" name="trigger_latitude"  id="triggerLat" value="{{ $advertisement->trigger_latitude }}">
                            <input type="hidden" name="trigger_longitude" id="triggerLng" value="{{ $advertisement->trigger_longitude }}">

                            <div class="mt-2 text-muted fs-13">
                                <i class="ri-map-pin-2-fill text-danger me-1"></i>
                                <span id="coordsText">
                                    {{ $advertisement->trigger_latitude }}, {{ $advertisement->trigger_longitude }}
                                </span>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="form-label">Trigger Radius</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" id="radiusSlider" name="radius_meters"
                                        class="form-range flex-grow-1"
                                        min="100" max="10000" step="100"
                                        value="{{ $advertisement->radius_meters }}">
                                    <span id="radiusLabel" class="badge bg-primary-subtle text-primary fs-13 fw-medium"
                                        style="min-width:70px;">
                                        {{ $advertisement->radius_meters >= 1000
                                            ? round($advertisement->radius_meters/1000, 1).' km'
                                            : $advertisement->radius_meters.' m' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Right Column --}}
                <div class="col-xl-4">

                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Link to Farm / Ranch / Event</h5></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Linked Type</label>
                                <select name="linked_type" id="linkedType" class="form-select">
                                    <option value="">— None —</option>
                                    <option value="farm"  {{ $advertisement->advertiseable_type === \App\Models\Farm::class  ? 'selected' : '' }}>Farm</option>
                                    <option value="ranch" {{ $advertisement->advertiseable_type === \App\Models\Ranch::class ? 'selected' : '' }}>Ranch</option>
                                    <option value="event" {{ $advertisement->advertiseable_type === \App\Models\Event::class ? 'selected' : '' }}>Event</option>
                                </select>
                            </div>
                            <div class="mb-3" id="linkedIdWrap">
                                <label class="form-label">Select <span id="linkedTypeLabel">Item</span></label>
                                <select name="linked_id" id="linkedId" class="form-select">
                                    <option value="">— Select —</option>
                                    @foreach($farms as $f)
                                        <option value="{{ $f->id }}"
                                            data-lat="{{ $f->latitude }}" data-lng="{{ $f->longitude }}"
                                            data-type="farm"
                                            {{ ($advertisement->advertiseable_type === \App\Models\Farm::class && $advertisement->advertiseable_id == $f->id) ? 'selected' : '' }}>
                                            {{ $f->name }}
                                        </option>
                                    @endforeach
                                    @foreach($ranches as $r)
                                        <option value="{{ $r->id }}"
                                            data-lat="{{ $r->latitude }}" data-lng="{{ $r->longitude }}"
                                            data-type="ranch"
                                            {{ ($advertisement->advertiseable_type === \App\Models\Ranch::class && $advertisement->advertiseable_id == $r->id) ? 'selected' : '' }}>
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                    @foreach($events as $e)
                                        <option value="{{ $e->id }}"
                                            data-lat="{{ $e->latitude }}" data-lng="{{ $e->longitude }}"
                                            data-type="event"
                                            {{ ($advertisement->advertiseable_type === \App\Models\Event::class && $advertisement->advertiseable_id == $e->id) ? 'selected' : '' }}>
                                            {{ $e->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Schedule</h5></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="text" name="starts_at" class="form-control"
                                    data-provider="flatpickr" data-date-format="Y-m-d"
                                    value="{{ $advertisement->starts_at?->format('Y-m-d') }}"
                                    placeholder="Always on">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="text" name="ends_at" class="form-control"
                                    data-provider="flatpickr" data-date-format="Y-m-d"
                                    value="{{ $advertisement->ends_at?->format('Y-m-d') }}"
                                    placeholder="No expiry">
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Status</h5></div>
                        <div class="card-body">
                            <select name="status" class="form-select">
                                <option value="active"   {{ $advertisement->status === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $advertisement->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="ri-save-line me-1"></i> Update Advertisement
                            </button>
                            <a href="{{ route('admin.advertisements.show', $advertisement->id) }}" class="btn btn-light">Cancel</a>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap" async defer></script>
<script>
var map, marker, circle;
var INIT_LAT = {{ $advertisement->trigger_latitude }};
var INIT_LNG = {{ $advertisement->trigger_longitude }};
var INIT_R   = {{ $advertisement->radius_meters }};

function initMap() {
    map = new google.maps.Map(document.getElementById('triggerMap'), {
        center: { lat: INIT_LAT, lng: INIT_LNG },
        zoom: 13,
    });
    placeMarker(INIT_LAT, INIT_LNG);

    var autocomplete = new google.maps.places.Autocomplete(document.getElementById('locationSearch'));
    autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            map.setCenter(place.geometry.location);
            map.setZoom(13);
            placeMarker(place.geometry.location.lat(), place.geometry.location.lng());
        }
    });

    map.addListener('click', function (e) {
        placeMarker(e.latLng.lat(), e.latLng.lng());
    });
}

function placeMarker(lat, lng) {
    document.getElementById('triggerLat').value = lat;
    document.getElementById('triggerLng').value = lng;
    document.getElementById('coordsText').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);

    var pos = { lat: lat, lng: lng };
    if (marker) { marker.setPosition(pos); } else {
        marker = new google.maps.Marker({ position: pos, map: map, draggable: true });
        marker.addListener('dragend', function (e) { placeMarker(e.latLng.lat(), e.latLng.lng()); });
    }
    var r = parseInt(document.getElementById('radiusSlider').value);
    if (circle) { circle.setCenter(pos); circle.setRadius(r); } else {
        circle = new google.maps.Circle({
            map: map, center: pos, radius: r,
            fillColor: '#405189', fillOpacity: 0.15,
            strokeColor: '#405189', strokeOpacity: 0.6, strokeWeight: 1,
        });
    }
}

$(function () {
    $('#radiusSlider').on('input', function () {
        var v = parseInt(this.value);
        $('#radiusLabel').text(v >= 1000 ? (v/1000).toFixed(1)+' km' : v+' m');
        if (circle) circle.setRadius(v);
    });

    $('#imageInput').on('change', function () {
        var file = this.files[0];
        if (! file) return;
        var reader = new FileReader();
        reader.onload = function (e) { $('#imagePreview').attr('src', e.target.result); };
        reader.readAsDataURL(file);
    });

    $('#adForm').on('submit', function (e) {
        e.preventDefault();
        var btn = $('#submitBtn').prop('disabled', true).text('Saving...');
        var data = new FormData(this);

        $.ajax({
            url: '{{ route("admin.advertisements.update", $advertisement->id) }}',
            method: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success(res.message);
                setTimeout(function () {
                    window.location.href = '{{ route("admin.advertisements.show", $advertisement->id) }}';
                }, 800);
            },
            error: function (xhr) {
                btn.prop('disabled', false).text('Update Advertisement');
                var errors = xhr.responseJSON?.errors ?? {};
                Object.values(errors).flat().forEach(function (msg) { toastr.error(msg); });
            }
        });
    });
});
</script>
@endpush
