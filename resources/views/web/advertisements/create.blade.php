{{-- resources/views/web/advertisements/create.blade.php --}}
@extends('layout.master-layout')
@section('title', 'Create Advertisement')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Create Advertisement</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.advertisements.index') }}">Advertisements</a></li>
                                <li class="breadcrumb-item active">Create</li>
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

                        {{-- Basic Info --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Ad Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control"
                                            placeholder="e.g. Visit Green Valley Farm" maxlength="150">
                                        <div class="text-danger small mt-1" id="error-title"></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Subtitle</label>
                                        <input type="text" name="subtitle" id="subtitle" class="form-control"
                                            placeholder="Short description or tagline" maxlength="255">
                                        <div class="text-danger small mt-1" id="error-subtitle"></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">CTA Button Label</label>
                                        <input type="text" name="cta_label" id="cta_label" class="form-control"
                                            value="View Details" maxlength="50">
                                        <div class="form-text">Text shown on the button inside the ad banner.</div>
                                        <div class="text-danger small mt-1" id="error-cta_label"></div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Banner Image --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Banner Image <span class="text-danger">*</span></h5>
                            </div>
                            <div class="card-body">

                                {{-- Preview --}}
                                <div class="text-center mb-3">
                                    <img id="imagePreview"
                                        src="{{ asset('admin/default/placeholder.png') }}"
                                        class="img-fluid rounded border"
                                        style="max-height:160px;object-fit:cover;width:100%;"
                                        alt="Banner Preview">
                                </div>

                                <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                                <div class="form-text">JPG, PNG, WEBP — max 2 MB. Recommended: 600×200 px (3:1 ratio)</div>
                                <div class="text-danger small mt-1" id="error-image"></div>
                            </div>
                        </div>

                        {{-- Trigger Location Map --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Trigger Location <span class="text-danger">*</span></h5>
                                <small class="text-muted">Click on the map to set where this ad will appear</small>
                            </div>
                            <div class="card-body">

                                {{-- Search --}}
                                <div class="mb-3">
                                    <label class="form-label">Search on Map</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="ri-map-pin-line text-muted"></i>
                                        </span>
                                        <input type="text" id="locationSearch" class="form-control"
                                            placeholder="Search address or place...">
                                    </div>
                                    <small class="text-muted">Search for an address OR click directly on the map to pin the location.</small>
                                </div>

                                {{-- Map --}}
                                <div id="triggerMap" style="height:380px;border-radius:8px;border:1px solid #dee2e6;cursor:crosshair;"></div>

                                {{-- Hidden inputs --}}
                                <input type="hidden" name="trigger_latitude"  id="triggerLat">
                                <input type="hidden" name="trigger_longitude" id="triggerLng">

                                {{-- Coords display --}}
                                <div id="coordsDisplay" class="mt-2 text-muted fs-13 d-none">
                                    <i class="ri-map-pin-2-fill text-danger me-1"></i>
                                    <span id="coordsText"></span>
                                </div>

                                <div class="text-danger small mt-1" id="error-trigger_latitude"></div>

                                {{-- Radius Slider --}}
                                <div class="mt-3">
                                    <label class="form-label">Trigger Radius</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="range" id="radiusSlider" name="radius_meters"
                                            class="form-range flex-grow-1"
                                            min="100" max="10000" step="100" value="1000">
                                        <span id="radiusLabel"
                                            class="badge bg-primary-subtle text-primary fs-13 fw-medium"
                                            style="min-width:70px;">1.0 km</span>
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
                            <div class="card-header">
                                <h5 class="card-title mb-0">Link to Farm / Ranch / Event</h5>
                            </div>
                            <div class="card-body">

                                <div class="mb-3">
                                    <label class="form-label">Linked Type</label>
                                    <select name="linked_type" id="linkedType" class="form-select">
                                        <option value="">— None —</option>
                                        <option value="farm">Farm</option>
                                        <option value="ranch">Ranch</option>
                                        <option value="event">Event</option>
                                    </select>
                                    <div class="text-danger small mt-1" id="error-linked_type"></div>
                                </div>

                                <div class="mb-3 d-none" id="linkedIdWrap">
                                    <label class="form-label">Select <span id="linkedTypeLabel">Item</span></label>
                                    <select name="linked_id" id="linkedId" class="form-select">
                                        <option value="">— Select —</option>
                                    </select>
                                    <div class="text-danger small mt-1" id="error-linked_id"></div>
                                </div>

                                <button type="button" id="autoFillBtn"
                                    class="btn btn-soft-info btn-sm d-none w-100 mt-1">
                                    <i class="ri-crosshairs-2-line me-1"></i> Use Entity Location as Trigger
                                </button>

                            </div>
                        </div>

                        {{-- Schedule --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Schedule</h5>
                            </div>
                            <div class="card-body">

                                <div class="mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="text" name="starts_at" id="starts_at"
                                        class="form-control"
                                        data-provider="flatpickr"
                                        data-date-format="Y-m-d"
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
                                        placeholder="YYYY-MM-DD (leave blank = no expiry)">
                                    <div class="form-text">Format: YYYY-MM-DD</div>
                                    <div class="text-danger small mt-1" id="error-ends_at"></div>
                                </div>

                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Status</h5>
                            </div>
                            <div class="card-body">
                                <select name="status" id="ad_status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                <div class="text-danger small mt-1" id="error-ad_status"></div>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="card">
                            <div class="card-body">
                                <div class="hstack gap-2">
                                    <button type="submit" class="btn btn-success w-100" id="submitBtn">
                                        <span id="submitBtnText">
                                            <i class="ri-save-line me-1"></i> Create Advertisement
                                        </span>
                                        <span id="submitBtnSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                            Saving…
                                        </span>
                                    </button>
                                    <a href="{{ route('admin.advertisements.index') }}" class="btn btn-light">Cancel</a>
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
    const s = document.createElement('script');
    s.src = `https://maps.googleapis.com/maps/api/js?key=${window.__GOOGLE_MAPS_KEY}&libraries=places&callback=initAdMap`;
    s.async = true;
    s.defer = true;
    document.head.appendChild(s);
})();

var adMap, adMarker, adCircle;

window.initAdMap = function () {
    adMap = new google.maps.Map(document.getElementById('triggerMap'), {
        center: { lat: 39.5, lng: -98.35 },
        zoom: 4,
    });

    // Click to place marker
    adMap.addListener('click', function (e) {
        placeAdMarker(e.latLng.lat(), e.latLng.lng());
    });

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
};

function placeAdMarker(lat, lng) {
    document.getElementById('triggerLat').value = lat;
    document.getElementById('triggerLng').value = lng;

    // Clear error if set
    document.getElementById('error-trigger_latitude').textContent = '';

    var pos = { lat: lat, lng: lng };

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

    var radius = parseInt(document.getElementById('radiusSlider').value);
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

    document.getElementById('coordsDisplay').classList.remove('d-none');
    document.getElementById('coordsText').textContent = lat.toFixed(6) + ', ' + lng.toFixed(6);
}
</script>

{{-- Entity data --}}
<script>
var ENTITY_MAP = {
    farm  : @json($farms),
    ranch : @json($ranches),
    event : @json($events),
};
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Radius slider ──────────────────────────────────────────────────────
    document.getElementById('radiusSlider').addEventListener('input', function () {
        var v     = parseInt(this.value);
        var label = v >= 1000 ? (v / 1000).toFixed(1) + ' km' : v + ' m';
        document.getElementById('radiusLabel').textContent = label;
        if (adCircle) adCircle.setRadius(v);
    });

    // ── Banner image preview ───────────────────────────────────────────────
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
    document.getElementById('linkedType').addEventListener('change', function () {
        var type      = this.value;
        var idWrap    = document.getElementById('linkedIdWrap');
        var autoBtn   = document.getElementById('autoFillBtn');
        var typeLabel = document.getElementById('linkedTypeLabel');
        var select    = document.getElementById('linkedId');

        if (!type) {
            idWrap.classList.add('d-none');
            autoBtn.classList.add('d-none');
            return;
        }

        typeLabel.textContent = type.charAt(0).toUpperCase() + type.slice(1);
        var entities = ENTITY_MAP[type] || [];
        var opts = '<option value="">— Select —</option>';
        entities.forEach(function (item) {
            opts += '<option value="' + item.id + '" data-lat="' + item.lat + '" data-lng="' + item.lng + '">'
                + item.name + '</option>';
        });
        select.innerHTML = opts;
        idWrap.classList.remove('d-none');
        autoBtn.classList.add('d-none');
    });

    document.getElementById('linkedId').addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        var lat = opt.dataset.lat;
        var lng = opt.dataset.lng;
        var btn = document.getElementById('autoFillBtn');
        if (lat && lng) btn.classList.remove('d-none');
        else            btn.classList.add('d-none');
    });

    document.getElementById('autoFillBtn').addEventListener('click', function () {
        var opt = document.getElementById('linkedId').options[document.getElementById('linkedId').selectedIndex];
        var lat = parseFloat(opt.dataset.lat);
        var lng = parseFloat(opt.dataset.lng);
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

        // Manual trigger location check
        if (!document.getElementById('triggerLat').value) {
            document.getElementById('error-trigger_latitude').textContent =
                'Please click the map to set a trigger location.';
            document.getElementById('triggerMap').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        setLoading(true);

        var fd = new FormData(this);

        axios.post('{{ route('admin.advertisements.store') }}', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(function (res) {
            // ── FIX 1: redirect after success ──────────────────────────
            Toast.success(res.data.message);
            setTimeout(function () {
                window.location.href = res.data.data.redirect;
            }, 800);
        })
        .catch(function (err) {
            var data = err.response?.data;
            if (data?.errors) {
                // ── FIX 3: show field-level errors ──────────────────────
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
