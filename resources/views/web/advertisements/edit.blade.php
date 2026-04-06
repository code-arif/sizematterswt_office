{{-- resources/views/web/advertisements/edit.blade.php --}}
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

        <form id="adForm" novalidate>
            @csrf

            <div class="row g-4">

                {{-- ── Left Column ───────────────────────────────────────── --}}
                <div class="col-lg-8">

                    {{-- Ad Info --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Ad Information</h5></div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="form-control"
                                        value="{{ $advertisement->title }}" maxlength="150">
                                    <div class="text-danger small mt-1" id="error-title"></div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Subtitle</label>
                                    <input type="text" name="subtitle" id="subtitle" class="form-control"
                                        value="{{ $advertisement->subtitle }}" maxlength="255">
                                    <div class="text-danger small mt-1" id="error-subtitle"></div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">CTA Button Label</label>
                                    <input type="text" name="cta_label" id="cta_label" class="form-control"
                                        value="{{ $advertisement->cta_label }}" maxlength="50">
                                    <div class="form-text">Text shown on the button inside the ad banner.</div>
                                    <div class="text-danger small mt-1" id="error-cta_label"></div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- Banner Image --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Banner Image</h5></div>
                        <div class="card-body">

                            <div class="mb-3">
                                <p class="text-muted fs-13 mb-1">Current Banner:</p>
                                <img src="{{ asset('storage/' . $advertisement->image) }}"
                                    id="imagePreview"
                                    class="img-fluid rounded border"
                                    style="max-height:160px;object-fit:cover;width:100%;"
                                    alt="Banner Preview">
                            </div>

                            <label class="form-label">Replace Image <span class="text-muted">(optional)</span></label>
                            <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                            <div class="form-text">Leave blank to keep current image. JPG, PNG, WEBP — max 2 MB.</div>
                            <div class="text-danger small mt-1" id="error-image"></div>

                        </div>
                    </div>

                    {{-- Trigger Location Map --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Trigger Location <span class="text-danger">*</span></h5>
                            <small class="text-muted">Click map to move the trigger point</small>
                        </div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Search on Map</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ri-map-pin-line text-muted"></i>
                                    </span>
                                    <input type="text" id="locationSearch" class="form-control"
                                        placeholder="Search address or place...">
                                </div>
                            </div>

                            <div id="triggerMap" style="height:380px;border-radius:8px;border:1px solid #dee2e6;cursor:crosshair;"></div>

                            <input type="hidden" name="trigger_latitude"  id="triggerLat"
                                value="{{ $advertisement->trigger_latitude }}">
                            <input type="hidden" name="trigger_longitude" id="triggerLng"
                                value="{{ $advertisement->trigger_longitude }}">

                            <div class="mt-2 text-muted fs-13">
                                <i class="ri-map-pin-2-fill text-danger me-1"></i>
                                <span id="coordsText">
                                    {{ number_format($advertisement->trigger_latitude, 6) }},
                                    {{ number_format($advertisement->trigger_longitude, 6) }}
                                </span>
                            </div>
                            <div class="text-danger small mt-1" id="error-trigger_latitude"></div>

                            {{-- Radius Slider --}}
                            <div class="mt-3">
                                <label class="form-label">Trigger Radius</label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" id="radiusSlider" name="radius_meters"
                                        class="form-range flex-grow-1"
                                        min="100" max="10000" step="100"
                                        value="{{ $advertisement->radius_meters }}">
                                    <span id="radiusLabel"
                                        class="badge bg-primary-subtle text-primary fs-13 fw-medium"
                                        style="min-width:70px;">
                                        {{ $advertisement->radius_meters >= 1000
                                            ? round($advertisement->radius_meters / 1000, 1) . ' km'
                                            : $advertisement->radius_meters . ' m' }}
                                    </span>
                                </div>
                                <div class="form-text">How far from the trigger point users will see this ad.</div>
                                <div class="text-danger small mt-1" id="error-radius_meters"></div>
                            </div>

                        </div>
                    </div>

                </div>
                {{-- /left col --}}

                {{-- ── Right Column ──────────────────────────────────────── --}}
                <div class="col-lg-4">

                    {{-- Link to Entity --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Link to Farm / Ranch / Event</h5></div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Linked Type</label>
                                <select name="linked_type" id="linkedType" class="form-select">
                                    <option value="">— None —</option>
                                    <option value="farm"
                                        {{ $advertisement->advertiseable_type === \App\Models\Farm::class  ? 'selected' : '' }}>
                                        Farm
                                    </option>
                                    <option value="ranch"
                                        {{ $advertisement->advertiseable_type === \App\Models\Ranche::class ? 'selected' : '' }}>
                                        Ranch
                                    </option>
                                    <option value="event"
                                        {{ $advertisement->advertiseable_type === \App\Models\Event::class ? 'selected' : '' }}>
                                        Event
                                    </option>
                                </select>
                                <div class="text-danger small mt-1" id="error-linked_type"></div>
                            </div>

                            <div class="mb-3" id="linkedIdWrap">
                                <label class="form-label">Select <span id="linkedTypeLabel">Item</span></label>
                                <select name="linked_id" id="linkedId" class="form-select">
                                    <option value="">— Select —</option>
                                    @foreach($farms as $f)
                                        <option value="{{ $f->id }}"
                                            data-lat="{{ $f->latitude }}"
                                            data-lng="{{ $f->longitude }}"
                                            data-type="farm"
                                            {{ ($advertisement->advertiseable_type === \App\Models\Farm::class && $advertisement->advertiseable_id == $f->id) ? 'selected' : '' }}>
                                            {{ $f->name }}
                                        </option>
                                    @endforeach
                                    @foreach($ranches as $r)
                                        <option value="{{ $r->id }}"
                                            data-lat="{{ $r->latitude }}"
                                            data-lng="{{ $r->longitude }}"
                                            data-type="ranch"
                                            {{ ($advertisement->advertiseable_type === \App\Models\Ranche::class && $advertisement->advertiseable_id == $r->id) ? 'selected' : '' }}>
                                            {{ $r->name }}
                                        </option>
                                    @endforeach
                                    @foreach($events as $e)
                                        <option value="{{ $e->id }}"
                                            data-lat="{{ $e->latitude }}"
                                            data-lng="{{ $e->longitude }}"
                                            data-type="event"
                                            {{ ($advertisement->advertiseable_type === \App\Models\Event::class && $advertisement->advertiseable_id == $e->id) ? 'selected' : '' }}>
                                            {{ $e->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="text-danger small mt-1" id="error-linked_id"></div>
                            </div>

                            <button type="button" id="autoFillBtn"
                                class="btn btn-soft-info btn-sm d-none w-100">
                                <i class="ri-crosshairs-2-line me-1"></i> Use Entity Location as Trigger
                            </button>

                        </div>
                    </div>

                    {{-- Schedule --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Schedule</h5></div>
                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="text" name="starts_at" id="starts_at"
                                    class="form-control"
                                    data-provider="flatpickr"
                                    data-date-format="Y-m-d"
                                    value="{{ $advertisement->starts_at?->format('Y-m-d') }}"
                                    placeholder="YYYY-MM-DD (leave blank = always on)">
                                <div class="form-text">Format: YYYY-MM-DD</div>
                                <div class="text-danger small mt-1" id="error-starts_at"></div>
                            </div>

                            <div class="mb-1">
                                <label class="form-label">End Date</label>
                                <input type="text" name="ends_at" id="ends_at"
                                    class="form-control"
                                    data-provider="flatpickr"
                                    data-date-format="Y-m-d"
                                    value="{{ $advertisement->ends_at?->format('Y-m-d') }}"
                                    placeholder="YYYY-MM-DD (leave blank = no expiry)">
                                <div class="form-text">Format: YYYY-MM-DD</div>
                                <div class="text-danger small mt-1" id="error-ends_at"></div>
                            </div>

                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="card">
                        <div class="card-header"><h5 class="card-title mb-0">Status</h5></div>
                        <div class="card-body">
                            <select name="status" id="ad_c_status" class="form-select">
                                <option value="active"   {{ $advertisement->status === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $advertisement->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            <div class="text-danger small mt-1" id="error-ad_c_status"></div>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="card">
                        <div class="card-body">
                            <div class="hstack gap-2">
                                <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                    <span id="submitBtnText">
                                        <i class="ri-save-line me-1"></i> Update Advertisement
                                    </span>
                                    <span id="submitBtnSpinner" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                        Saving…
                                    </span>
                                </button>
                                <a href="{{ route('admin.advertisements.show', $advertisement->id) }}"
                                    class="btn btn-light">Cancel</a>
                            </div>
                        </div>
                    </div>

                </div>
                {{-- /right col --}}

            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    #triggerMap { cursor: crosshair; }
    .pac-container { z-index: 9999 !important; }
</style>
@endpush

@push('scripts')
{{-- Google Maps --}}
<script>
    window.__GOOGLE_MAPS_KEY = '{{ env('GOOGLE_MAPS_API_KEY') }}';
</script>
<script>
(function () {
    var s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=' + window.__GOOGLE_MAPS_KEY + '&libraries=places&callback=initAdMap';
    s.async = true;
    s.defer = true;
    document.head.appendChild(s);
})();

var adMap, adMarker, adCircle;
var INIT_LAT = {{ $advertisement->trigger_latitude }};
var INIT_LNG = {{ $advertisement->trigger_longitude }};

window.initAdMap = function () {
    adMap = new google.maps.Map(document.getElementById('triggerMap'), {
        center: { lat: INIT_LAT, lng: INIT_LNG },
        zoom  : 13,
    });

    // Place existing marker on load
    placeAdMarker(INIT_LAT, INIT_LNG);

    // Autocomplete search
    var autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('locationSearch'),
        { types: ['geocode', 'establishment'] }
    );
    autocomplete.bindTo('bounds', adMap);
    autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        if (place.geometry) {
            adMap.setCenter(place.geometry.location);
            adMap.setZoom(13);
            placeAdMarker(place.geometry.location.lat(), place.geometry.location.lng());
        }
    });

    // Click map
    adMap.addListener('click', function (e) {
        placeAdMarker(e.latLng.lat(), e.latLng.lng());
    });
};

function placeAdMarker(lat, lng) {
    document.getElementById('triggerLat').value = lat;
    document.getElementById('triggerLng').value = lng;
    document.getElementById('coordsText').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
    document.getElementById('error-trigger_latitude').textContent = '';

    var pos = { lat: lat, lng: lng };
    var radius = parseInt(document.getElementById('radiusSlider').value);

    if (adMarker) {
        adMarker.setPosition(pos);
    } else {
        adMarker = new google.maps.Marker({
            position : pos,
            map      : adMap,
            draggable: true,
            animation: google.maps.Animation.DROP,
        });
        adMarker.addListener('dragend', function (e) {
            placeAdMarker(e.latLng.lat(), e.latLng.lng());
        });
    }

    if (adCircle) {
        adCircle.setCenter(pos);
        adCircle.setRadius(radius);
    } else {
        adCircle = new google.maps.Circle({
            map          : adMap,
            center       : pos,
            radius       : radius,
            fillColor    : '#405189',
            fillOpacity  : 0.15,
            strokeColor  : '#405189',
            strokeOpacity: 0.6,
            strokeWeight : 1,
        });
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Radius slider ──────────────────────────────────────────────────────
    document.getElementById('radiusSlider').addEventListener('input', function () {
        var v = parseInt(this.value);
        document.getElementById('radiusLabel').textContent =
            v >= 1000 ? (v / 1000).toFixed(1) + ' km' : v + ' m';
        if (adCircle) adCircle.setRadius(v);
    });

    // ── Image preview ──────────────────────────────────────────────────────
    document.getElementById('imageInput').addEventListener('change', function () {
        var file = this.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('imagePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    // ── Linked type selector ───────────────────────────────────────────────
    // Show auto-fill button when an option with coords is selected
    document.getElementById('linkedId').addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        var lat = opt.dataset.lat;
        var lng = opt.dataset.lng;
        var btn = document.getElementById('autoFillBtn');
        if (lat && lng) btn.classList.remove('d-none');
        else            btn.classList.add('d-none');
    });

    document.getElementById('linkedType').addEventListener('change', function () {
        document.getElementById('autoFillBtn').classList.add('d-none');
    });

    document.getElementById('autoFillBtn').addEventListener('click', function () {
        var select = document.getElementById('linkedId');
        var opt    = select.options[select.selectedIndex];
        var lat    = parseFloat(opt.dataset.lat);
        var lng    = parseFloat(opt.dataset.lng);
        if (!lat || !lng) return;
        adMap.setCenter({ lat: lat, lng: lng });
        adMap.setZoom(13);
        placeAdMarker(lat, lng);
    });

    // ── Helpers ────────────────────────────────────────────────────────────
    function clearErrors() {
        document.querySelectorAll('[id^="error-"]').forEach(function (el) {
            el.textContent = '';
        });
        document.querySelectorAll('.is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
    }

    function showFieldErrors(errors) {
        Object.entries(errors).forEach(function (entry) {
            var field    = entry[0];
            var messages = entry[1];
            var errEl    = document.getElementById('error-' + field);
            var inputEl  = document.getElementById(field);
            if (errEl)   errEl.textContent = messages[0];
            if (inputEl) inputEl.classList.add('is-invalid');
        });

        // Scroll to first error
        var firstErr = document.querySelector('.is-invalid');
        if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function setLoading(state) {
        document.getElementById('submitBtn').disabled = state;
        document.getElementById('submitBtnText').classList.toggle('d-none', state);
        document.getElementById('submitBtnSpinner').classList.toggle('d-none', !state);
    }

    // ── Form submit ────────────────────────────────────────────────────────
    document.getElementById('adForm').addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();

        // Trigger location required check
        if (!document.getElementById('triggerLat').value) {
            document.getElementById('error-trigger_latitude').textContent =
                'Please click the map to set a trigger location.';
            document.getElementById('triggerMap').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        setLoading(true);

        var fd = new FormData(this);
        fd.append('_method', 'POST'); // Laravel method spoofing via FormData

        axios.post('{{ route('admin.advertisements.update', $advertisement->id) }}', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(function (res) {
            Toast.success(res.data.message);
            setTimeout(function () {
                // ── Redirect to show page after update ──────────────────
                window.location.href = '{{ route('admin.advertisements.show', $advertisement->id) }}';
            }, 800);
        })
        .catch(function (err) {
            var data = err.response?.data;
            if (data?.errors) {
                showFieldErrors(data.errors);
                if (data.message) Toast.error(data.message);
            } else {
                Toast.fromResponse(data);
            }
        })
        .finally(function () {
            setLoading(false);
        });
    });

});
</script>
@endpush
