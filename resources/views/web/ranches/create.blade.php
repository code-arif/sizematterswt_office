@extends('layout.master-layout')

@section('title', 'Add Ranch')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Add New Ranch</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.ranches.index') }}">Ranches</a></li>
                                <li class="breadcrumb-item active">Add New</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form id="ranchForm" novalidate>
                @csrf
                <div class="row g-4">

                    {{-- ── Left column ─────────────────────────────────────────── --}}
                    <div class="col-lg-8">

                        {{-- Basic Info --}}
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Basic Information</h5></div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label for="name" class="form-label">Ranch Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter ranch name">
                                        <div class="text-danger small mt-1" id="error-name"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description"
                                            rows="3" placeholder="Brief description about this ranch…" maxlength="1000"></textarea>
                                        <div class="d-flex justify-content-between mt-1">
                                            <div class="text-danger small" id="error-description"></div>
                                            <small class="text-muted"><span id="descCount">0</span>/1000</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            placeholder="+1 (555) 000-0000">
                                        <div class="text-danger small mt-1" id="error-phone"></div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="ranch@example.com">
                                        <div class="text-danger small mt-1" id="error-email"></div>
                                    </div>

                                    <div class="col-lg-8">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" class="form-control" id="website" name="website"
                                            placeholder="https://ranchwebsite.com">
                                        <div class="text-danger small mt-1" id="error-website"></div>
                                    </div>

                                    {{-- Acreage — Ranch-specific field --}}
                                    <div class="col-lg-4">
                                        <label for="acreage" class="form-label">Acreage
                                            <small class="text-muted">(acres)</small>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="acreage" name="acreage"
                                                placeholder="0.00" min="0" step="0.01">
                                            <span class="input-group-text">ac</span>
                                        </div>
                                        <div class="text-danger small mt-1" id="error-acreage"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="tags" class="form-label">Tags
                                            <small class="text-muted">(comma-separated, e.g. cattle, horses)</small>
                                        </label>
                                        <input type="text" class="form-control" id="tags" name="tags"
                                            placeholder="cattle, horses, hunting">
                                        <div class="text-danger small mt-1" id="error-tags"></div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Location / Map --}}
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Location</h5></div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label class="form-label">Search on Map</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">
                                                <i class="ri-map-pin-line text-muted"></i>
                                            </span>
                                            <input type="text" id="mapSearchInput" class="form-control"
                                                placeholder="Search address or place name…">
                                        </div>
                                        <small class="text-muted">Search for an address OR click directly on the map to pin the location.</small>
                                    </div>

                                    <div class="col-12">
                                        <div id="ranchMap" style="width:100%;height:400px;border-radius:8px;border:1px solid #dee2e6;"></div>
                                    </div>

                                    <input type="hidden" id="latitude"  name="latitude">
                                    <input type="hidden" id="longitude" name="longitude">

                                    <div class="col-12">
                                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            placeholder="Full street address">
                                        <div class="text-danger small mt-1" id="error-address"></div>
                                    </div>

                                    <div class="col-lg-4">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" placeholder="City">
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" name="state" placeholder="State">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="zip_code" class="form-label">ZIP</label>
                                        <input type="text" class="form-control" id="zip_code" name="zip_code" placeholder="00000">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" name="country" value="US">
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label">Latitude</label>
                                        <input type="text" class="form-control bg-light" id="latDisplay" readonly placeholder="Click map to capture">
                                        <div class="text-danger small mt-1" id="error-latitude"></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Longitude</label>
                                        <input type="text" class="form-control bg-light" id="lngDisplay" readonly placeholder="Click map to capture">
                                        <div class="text-danger small mt-1" id="error-longitude"></div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    {{-- /left col --}}

                    {{-- ── Right column ────────────────────────────────────────── --}}
                    <div class="col-lg-4">

                        {{-- Publish Settings --}}
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Publish Settings</h5></div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label class="form-label">Status</label>
                                        <div class="d-flex flex-column gap-2">

                                            <label class="status-card active-card selected" for="status_active">
                                                <input type="radio" name="status" id="status_active" value="active" checked class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon"><i class="ri-checkbox-circle-fill"></i></span>
                                                    <div>
                                                        <p class="mb-0 fw-semibold">Active</p>
                                                        <small>Visible on the map for all users</small>
                                                    </div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                            <label class="status-card inactive-card" for="status_inactive">
                                                <input type="radio" name="status" id="status_inactive" value="inactive" class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon"><i class="ri-forbid-line"></i></span>
                                                    <div>
                                                        <p class="mb-0 fw-semibold">Inactive</p>
                                                        <small>Hidden from map and users</small>
                                                    </div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                            <label class="status-card pending-card" for="status_pending">
                                                <input type="radio" name="status" id="status_pending" value="pending" class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon"><i class="ri-time-line"></i></span>
                                                    <div>
                                                        <p class="mb-0 fw-semibold">Pending</p>
                                                        <small>Awaiting review before publishing</small>
                                                    </div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                        </div>
                                        <div class="text-danger small mt-1" id="error-status"></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="featured-toggle-card" for="is_featured" id="featuredCard">
                                            <input class="d-none" type="checkbox" id="is_featured" name="is_featured" value="1">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="featured-icon"><i class="ri-star-fill"></i></span>
                                                <div>
                                                    <p class="mb-0 fw-semibold">Featured Ranch</p>
                                                    <small>Pinned at the top of the map listings</small>
                                                </div>
                                            </div>
                                            <div class="featured-switch"><span class="switch-knob"></span></div>
                                        </label>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="hstack gap-2">
                                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                                <span id="submitBtnText"><i class="ri-save-line me-1"></i> Save Ranch</span>
                                                <span id="submitBtnSpinner" class="d-none">
                                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                    Saving…
                                                </span>
                                            </button>
                                            <a href="{{ route('admin.ranches.index') }}" class="btn btn-light">Cancel</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Thumbnail --}}
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Thumbnail</h5></div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img id="thumbnailPreview"
                                        src="{{ asset('admin/assets/images/default/ranch-placeholder.jpg') }}"
                                        class="img-fluid rounded" style="max-height:180px;object-fit:cover;width:100%;" alt="">
                                </div>
                                <label for="thumbnail" class="form-label">Upload Thumbnail</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail"
                                    accept="image/jpeg,image/png,image/webp">
                                <small class="text-muted">JPG, PNG, WEBP — max 2 MB</small>
                                <div class="text-danger small mt-1" id="error-thumbnail"></div>
                            </div>
                        </div>

                        {{-- Marker Settings --}}
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Map Marker</h5></div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label for="marker_color" class="form-label">Marker Color</label>
                                        <div class="input-group">
                                            <input type="color" class="form-control form-control-color"
                                                id="markerColorPicker" value="#8D4E0B" style="max-width:50px;">
                                            <input type="text" class="form-control" id="marker_color"
                                                name="marker_color" value="#8D4E0B" maxlength="7">
                                        </div>
                                        <div class="text-danger small mt-1" id="error-marker_color"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="marker_icon" class="form-label">Marker Icon Key</label>
                                        <input type="text" class="form-control" id="marker_icon" name="marker_icon"
                                            value="ranch_pin" placeholder="ranch_pin">
                                        <small class="text-muted">Used by Flutter to load the correct pin asset.</small>
                                        <div class="text-danger small mt-1" id="error-marker_icon"></div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Preview</label>
                                        <div id="markerPreview" class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded"
                                            style="background:#8D4E0B;">
                                            <i class="ri-map-pin-fill text-white fs-18"></i>
                                            <span class="text-white small fw-medium" id="markerPreviewLabel">ranch_pin</span>
                                        </div>
                                    </div>

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
    #ranchMap { cursor: crosshair; }
    .pac-container { z-index: 9999 !important; }

    .status-card {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 14px; border-radius: 8px; border: 1.5px solid #e9ebec;
        cursor: pointer; transition: all .2s ease; background: #fff; user-select: none;
    }
    .status-card:hover { border-color: #c8cdd5; }
    .status-card .status-icon {
        width: 36px; height: 36px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0;
    }
    .status-card small { color: #878a99; font-size: 12px; }
    .status-card p     { font-size: 14px; color: #212529; }
    .check-mark { font-size: 18px; opacity: 0; transition: opacity .15s; flex-shrink: 0; }

    .active-card .status-icon  { background: #d1f0ea; color: #0ab39c; }
    .active-card.selected      { border-color: #0ab39c; background: #f0fbf9; }
    .active-card.selected .check-mark { opacity: 1; color: #0ab39c; }

    .inactive-card .status-icon  { background: #e9ebec; color: #878a99; }
    .inactive-card.selected      { border-color: #878a99; background: #f8f9fa; }
    .inactive-card.selected .check-mark { opacity: 1; color: #878a99; }

    .pending-card .status-icon  { background: #fef3d0; color: #f7b84b; }
    .pending-card.selected      { border-color: #f7b84b; background: #fffdf3; }
    .pending-card.selected .check-mark { opacity: 1; color: #f7b84b; }

    .featured-toggle-card {
        display: flex; align-items: center; justify-content: space-between;
        padding: 12px 14px; border-radius: 8px; border: 1.5px solid #e9ebec;
        cursor: pointer; transition: all .2s ease; background: #fff; user-select: none; margin-top: 2px;
    }
    .featured-toggle-card:hover { border-color: #c8cdd5; }
    .featured-toggle-card.on    { border-color: #f7b84b; background: #fffdf3; }
    .featured-icon {
        width: 36px; height: 36px; border-radius: 50%; display: flex;
        align-items: center; justify-content: center; font-size: 17px;
        background: #fef3d0; color: #f7b84b; flex-shrink: 0;
    }
    .featured-toggle-card small { color: #878a99; font-size: 12px; }
    .featured-toggle-card p     { font-size: 14px; color: #212529; }
    .featured-switch {
        width: 42px; height: 22px; border-radius: 20px; background: #d2d6dc;
        position: relative; transition: background .2s; flex-shrink: 0;
    }
    .switch-knob {
        position: absolute; top: 3px; left: 3px; width: 16px; height: 16px;
        border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.2); transition: left .2s;
    }
    .featured-toggle-card.on .featured-switch { background: #f7b84b; }
    .featured-toggle-card.on .switch-knob     { left: 23px; }
</style>
@endpush

@push('scripts')
<script>
    window.__GOOGLE_MAPS_KEY = '{{ env('GOOGLE_MAPS_API_KEY') }}';
</script>
<script>
(function() {
    const s = document.createElement('script');
    s.src = `https://maps.googleapis.com/maps/api/js?key=${window.__GOOGLE_MAPS_KEY}&libraries=places&callback=initRanchMap`;
    s.async = true; s.defer = true;
    document.head.appendChild(s);
})();

let ranchMap, ranchMarker, geocoder, autocomplete;

window.initRanchMap = function () {
    const defaultCenter = { lat: 39.5, lng: -98.35 };

    ranchMap = new google.maps.Map(document.getElementById('ranchMap'), {
        zoom: 4, center: defaultCenter, mapTypeId: 'roadmap', streetViewControl: false,
    });
    geocoder = new google.maps.Geocoder();

    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('mapSearchInput'), { types: ['geocode', 'establishment'] }
    );
    autocomplete.bindTo('bounds', ranchMap);
    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (!place.geometry?.location) return;
        ranchMap.setCenter(place.geometry.location);
        ranchMap.setZoom(15);
        placeMarker(place.geometry.location);
        fillAddressFields(place.address_components, place.formatted_address);
    });

    ranchMap.addListener('click', function (event) {
        placeMarker(event.latLng);
        reverseGeocode(event.latLng);
    });
};

function placeMarker(location) {
    if (ranchMarker) {
        ranchMarker.setPosition(location);
    } else {
        ranchMarker = new google.maps.Marker({
            position: location, map: ranchMap, draggable: true,
            animation: google.maps.Animation.DROP,
        });
        ranchMarker.addListener('dragend', function (event) { reverseGeocode(event.latLng); updateLatLng(event.latLng); });
    }
    updateLatLng(location);
}

function updateLatLng(location) {
    const lat = location.lat(), lng = location.lng();
    document.getElementById('latitude').value  = lat;
    document.getElementById('longitude').value = lng;
    document.getElementById('latDisplay').value = lat.toFixed(7);
    document.getElementById('lngDisplay').value = lng.toFixed(7);
}

function reverseGeocode(latLng) {
    geocoder.geocode({ location: latLng }, function (results, status) {
        if (status === 'OK' && results[0]) fillAddressFields(results[0].address_components, results[0].formatted_address);
    });
}

function fillAddressFields(components, formattedAddress) {
    const get      = (type) => components.find(c => c.types.includes(type))?.long_name  || '';
    const getShort = (type) => components.find(c => c.types.includes(type))?.short_name || '';
    document.getElementById('address').value  = formattedAddress || '';
    document.getElementById('city').value     = get('locality') || get('administrative_area_level_2');
    document.getElementById('state').value    = get('administrative_area_level_1');
    document.getElementById('zip_code').value = get('postal_code');
    document.getElementById('country').value  = getShort('country') || 'US';
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Status cards ───────────────────────────────────────────────────── */
    document.querySelectorAll('.status-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.status-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    /* ── Featured toggle ────────────────────────────────────────────────── */
    const featuredCard     = document.getElementById('featuredCard');
    const featuredCheckbox = document.getElementById('is_featured');
    featuredCard.addEventListener('click', function () {
        featuredCheckbox.checked = !featuredCheckbox.checked;
        featuredCard.classList.toggle('on', featuredCheckbox.checked);
    });

    /* ── Char counter ───────────────────────────────────────────────────── */
    document.getElementById('description').addEventListener('input', function () {
        document.getElementById('descCount').textContent = this.value.length;
    });

    /* ── Thumbnail preview ──────────────────────────────────────────────── */
    document.getElementById('thumbnail').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => document.getElementById('thumbnailPreview').src = e.target.result;
        reader.readAsDataURL(file);
    });

    /* ── Marker color sync ──────────────────────────────────────────────── */
    const picker    = document.getElementById('markerColorPicker');
    const colorText = document.getElementById('marker_color');
    const preview   = document.getElementById('markerPreview');

    picker.addEventListener('input', function () {
        colorText.value = this.value;
        preview.style.background = this.value;
    });
    colorText.addEventListener('input', function () {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            picker.value = this.value;
            preview.style.background = this.value;
        }
    });
    document.getElementById('marker_icon').addEventListener('input', function () {
        document.getElementById('markerPreviewLabel').textContent = this.value || 'ranch_pin';
    });

    /* ── Helpers ────────────────────────────────────────────────────────── */
    function clearErrors() {
        document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }
    function showFieldErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const el    = document.getElementById('error-' + field);
            const input = document.getElementById(field);
            if (el) el.textContent = messages[0];
            if (input) input.classList.add('is-invalid');
        });
    }
    function setLoading(state) {
        document.getElementById('submitBtn').disabled = state;
        document.getElementById('submitBtnText').classList.toggle('d-none', state);
        document.getElementById('submitBtnSpinner').classList.toggle('d-none', !state);
    }

    /* ── Form submit ────────────────────────────────────────────────────── */
    document.getElementById('ranchForm').addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const fd = new FormData(this);
        fd.set('is_featured', document.getElementById('is_featured').checked ? 1 : 0);

        axios.post('{{ route('admin.ranches.store') }}', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(res => {
            Toast.success(res.data.message);
            setTimeout(() => window.location.href = res.data.data.redirect, 800);
        })
        .catch(err => {
            const data = err.response?.data;
            if (data?.errors) { showFieldErrors(data.errors); if (data.message) Toast.error(data.message); }
            else Toast.fromResponse(data);
        })
        .finally(() => setLoading(false));
    });

});
</script>
@endpush
