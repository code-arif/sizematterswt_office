@extends('layout.master-layout')

@section('title', 'Add Farm')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- ── Page Title ──────────────────────────────────────────────────── --}}
            <div class="row">
                                      {{-- Owner Information --}}
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Add New Farm</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.farms.index') }}">Farms</a></li>
                                <li class="breadcrumb-item active">Add New</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form id="farmForm" novalidate>
                @csrf
                <div class="row g-4">

                    {{-- ── Left column ─────────────────────────────────────────── --}}
                    <div class="col-lg-8">

                        <div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Owner Information</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Owner Name --}}
            <div class="col-lg-6">
                <label for="owner_name" class="form-label">Owner Name</label>
                <input type="text" class="form-control" id="owner_name" name="owner_name"
                    placeholder="Enter owner name">
                <div class="text-danger small mt-1" id="error-owner_name"></div>
            </div>

            {{-- Owner Phone --}}
            <div class="col-lg-6">
                <label for="owner_phone" class="form-label">Owner Phone</label>
                <input type="text" class="form-control" id="owner_phone" name="owner_phone"
                    placeholder="Enter owner phone">
                <div class="text-danger small mt-1" id="error-owner_phone"></div>
            </div>

            {{-- Owner Address --}}
            <div class="col-12">
                <label for="owner_address" class="form-label">Owner Address</label>
                <input type="text" class="form-control" id="owner_address" name="owner_address"
                    placeholder="Enter owner address">
                <div class="text-danger small mt-1" id="error-owner_address"></div>
            </div>

            {{-- Owner Avatar --}}
            <div class="col-12">
                <label for="owner_avatar" class="form-label">Owner Avatar</label>
                <div class="d-flex align-items-start gap-3">
                    <img id="ownerAvatarPreview"
                         src="{{ asset('admin/assets/images/users/user-dummy-img.jpg') }}"
                         class="rounded-circle avatar-lg img-thumbnail"
                         style="width: 80px; height: 80px; object-fit: cover;" alt="Owner Avatar">
                    <div class="flex-grow-1">
                        <input type="file" class="form-control" id="owner_avatar" name="owner_avatar"
                            accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">JPG, PNG, WEBP — max 2 MB</small>
                        <div class="text-danger small mt-1" id="error-owner_avatar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                        {{-- Basic Info --}}
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Basic Information</h5></div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label for="name" class="form-label">Farm Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter farm name">
                                        <div class="text-danger small mt-1" id="error-name"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description"
                                            rows="3" placeholder="Brief description about this farm…" maxlength="1000"></textarea>
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
                                            placeholder="farm@example.com">
                                        <div class="text-danger small mt-1" id="error-email"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" class="form-control" id="website" name="website"
                                            placeholder="https://farmwebsite.com">
                                        <div class="text-danger small mt-1" id="error-website"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="tags" class="form-label">Tags
                                            <small class="text-muted">(comma-separated, e.g. organic, dairy)</small>
                                        </label>
                                        <input type="text" class="form-control" id="tags" name="tags"
                                            placeholder="organic, dairy, free-range">
                                        <div class="text-danger small mt-1" id="error-tags"></div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Location / Map ──────────────────────────────────────── --}}
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Location</h5></div>
                            <div class="card-body">
                                <div class="row g-3">

                                    {{-- Map search bar --}}
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

                                    {{-- Google Map canvas --}}
                                    <div class="col-12">
                                        <div id="farmMap" style="width:100%;height:400px;border-radius:8px;border:1px solid #dee2e6;"></div>
                                    </div>

                                    {{-- Hidden lat/lng (auto-filled by map) --}}
                                    <input type="hidden" id="latitude"  name="latitude">
                                    <input type="hidden" id="longitude" name="longitude">

                                    {{-- Editable address fields (auto-filled by reverse geocode, editable) --}}
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
                                        <input type="text" class="form-control" id="country" name="country" placeholder="US" value="US">
                                    </div>

                                    {{-- Lat/Lng display --}}
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
                                        <div class="d-flex flex-column gap-2" id="statusGroup">

                                            <label class="status-card active-card selected" for="status_active">
                                                <input type="radio" name="status" id="status_active"
                                                    value="active" checked class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon">
                                                        <i class="ri-checkbox-circle-fill"></i>
                                                    </span>
                                                    <div>
                                                        <p class="mb-0 fw-semibold">Active</p>
                                                        <small>Visible on the map for all users</small>
                                                    </div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                            <label class="status-card inactive-card" for="status_inactive">
                                                <input type="radio" name="status" id="status_inactive"
                                                    value="inactive" class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon">
                                                        <i class="ri-forbid-line"></i>
                                                    </span>
                                                    <div>
                                                        <p class="mb-0 fw-semibold">Inactive</p>
                                                        <small>Hidden from map and users</small>
                                                    </div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                            <label class="status-card pending-card" for="status_pending">
                                                <input type="radio" name="status" id="status_pending"
                                                    value="pending" class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon">
                                                        <i class="ri-time-line"></i>
                                                    </span>
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
                                            <input class="d-none" type="checkbox" id="is_featured"
                                                name="is_featured" value="1">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="featured-icon">
                                                    <i class="ri-star-fill"></i>
                                                </span>
                                                <div>
                                                    <p class="mb-0 fw-semibold">Featured Farm</p>
                                                    <small>Pinned at the top of the map listings</small>
                                                </div>
                                            </div>
                                            <div class="featured-switch">
                                                <span class="switch-knob"></span>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="hstack gap-2">
                                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                                <span id="submitBtnText"><i class="ri-save-line me-1"></i> Save Farm</span>
                                                <span id="submitBtnSpinner" class="d-none">
                                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                    Saving…
                                                </span>
                                            </button>
                                            <a href="{{ route('admin.farms.index') }}" class="btn btn-light">Cancel</a>
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
                                        src="{{ asset('admin/default/placeholder.png') }}"
                                        class="img-fluid rounded" style="max-height:180px;object-fit:cover;width:100%;"
                                        alt="Thumbnail Preview">
                                </div>
                                <label for="thumbnail" class="form-label">Upload Thumbnail</label>
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail"
                                    accept="image/jpeg,image/png,image/webp">
                                <small class="text-muted">JPG, PNG, WEBP — max 2 MB</small>
                                <div class="text-danger small mt-1" id="error-thumbnail"></div>
                            </div>
                        </div>

                        {{-- Gallery Media --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Farm Gallery (Images & Videos)</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="mediaInput" class="form-label">Upload Multiple Files</label>
                                    <input type="file" class="form-control" id="mediaInput" name="media[]"
                                        accept="image/*,video/*" multiple>
                                    <small class="text-muted">You can select multiple images or videos (max 10MB each).</small>
                                    <div class="text-danger small mt-1" id="error-media"></div>
                                </div>

                                {{-- Preview Container --}}
                                <div id="mediaPreviewRow" class="row g-2">
                                    {{-- JS will inject previews here --}}
                                </div>
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
                                                id="markerColorPicker" value="#2E7D32" title="Pick marker color"
                                                style="max-width:50px;">
                                            <input type="text" class="form-control" id="marker_color"
                                                name="marker_color" value="#2E7D32" placeholder="#2E7D32"
                                                maxlength="7">
                                        </div>
                                        <div class="text-danger small mt-1" id="error-marker_color"></div>
                                    </div>

                                    <div class="col-12 d-none">
                                        <label for="marker_icon" class="form-label">Marker Icon Key</label>
                                        <input type="text" class="form-control" id="marker_icon" name="marker_icon"
                                            value="farm_pin" placeholder="farm_pin">
                                        <small class="text-muted">Used by Flutter to load the correct pin asset.</small>
                                        <div class="text-danger small mt-1" id="error-marker_icon"></div>
                                    </div>

                                    {{-- Live preview --}}
                                    <div class="col-12 d-none">
                                        <label class="form-label">Preview</label>
                                        <div id="markerPreview" class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded"
                                            style="background:#2E7D32;">
                                            <i class="ri-map-pin-fill text-white fs-18"></i>
                                            <span class="text-white small fw-medium" id="markerPreviewLabel">farm_pin</span>
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
    #farmMap { cursor: crosshair; }
    .pac-container { z-index: 9999 !important; }

    /* ── Status radio cards ───────────────────────────────────────────── */
    .status-card {
        display        : flex;
        align-items    : center;
        justify-content: space-between;
        padding        : 12px 14px;
        border-radius  : 8px;
        border         : 1.5px solid #e9ebec;
        cursor         : pointer;
        transition     : all .2s ease;
        background     : #fff;
        user-select    : none;
    }
    .status-card:hover { border-color: #c8cdd5; }

    .status-card .status-icon {
        width          : 36px;
        height         : 36px;
        border-radius  : 50%;
        display        : flex;
        align-items    : center;
        justify-content: center;
        font-size      : 17px;
        flex-shrink    : 0;
    }
    .status-card small { color: #878a99; font-size: 12px; }
    .status-card p     { font-size: 14px; color: #212529; }

    .check-mark {
        font-size  : 18px;
        opacity    : 0;
        transition : opacity .15s;
        flex-shrink: 0;
    }

    /* Active state */
    .active-card .status-icon  { background: #d1f0ea; color: #0ab39c; }
    .active-card.selected      { border-color: #0ab39c; background: #f0fbf9; }
    .active-card.selected .check-mark { opacity: 1; color: #0ab39c; }

    /* Inactive state */
    .inactive-card .status-icon  { background: #e9ebec; color: #878a99; }
    .inactive-card.selected      { border-color: #878a99; background: #f8f9fa; }
    .inactive-card.selected .check-mark { opacity: 1; color: #878a99; }

    /* Pending state */
    .pending-card .status-icon  { background: #fef3d0; color: #f7b84b; }
    .pending-card.selected      { border-color: #f7b84b; background: #fffdf3; }
    .pending-card.selected .check-mark { opacity: 1; color: #f7b84b; }

    /* ── Featured toggle card ─────────────────────────────────────────── */
    .featured-toggle-card {
        display        : flex;
        align-items    : center;
        justify-content: space-between;
        padding        : 12px 14px;
        border-radius  : 8px;
        border         : 1.5px solid #e9ebec;
        cursor         : pointer;
        transition     : all .2s ease;
        background     : #fff;
        user-select    : none;
        margin-top     : 2px;
    }
    .featured-toggle-card:hover { border-color: #c8cdd5; }
    .featured-toggle-card.on    { border-color: #f7b84b; background: #fffdf3; }

    .featured-icon {
        width          : 36px;
        height         : 36px;
        border-radius  : 50%;
        display        : flex;
        align-items    : center;
        justify-content: center;
        font-size      : 17px;
        background     : #fef3d0;
        color          : #f7b84b;
        flex-shrink    : 0;
    }
    .featured-toggle-card small { color: #878a99; font-size: 12px; }
    .featured-toggle-card p     { font-size: 14px; color: #212529; }

    /* Custom switch */
    .featured-switch {
        width        : 42px;
        height       : 22px;
        border-radius: 20px;
        background   : #d2d6dc;
        position     : relative;
        transition   : background .2s;
        flex-shrink  : 0;
    }
    .switch-knob {
        position     : absolute;
        top          : 3px;
        left         : 3px;
        width        : 16px;
        height       : 16px;
        border-radius: 50%;
        background   : #fff;
        box-shadow   : 0 1px 3px rgba(0,0,0,.2);
        transition   : left .2s;
    }
    .featured-toggle-card.on .featured-switch { background: #f7b84b; }
    .featured-toggle-card.on .switch-knob     { left: 23px; }
</style>
@endpush

@push('scripts')
{{-- Google Maps API with Places library --}}
<script>
    window.__GOOGLE_MAPS_KEY = '{{ env('GOOGLE_MAPS_API_KEY') }}';
</script>
<script>
/* ── Lazy-load Google Maps API ──────────────────────────────────────────── */
(function() {
    const s = document.createElement('script');
    s.src = `https://maps.googleapis.com/maps/api/js?key=${window.__GOOGLE_MAPS_KEY}&libraries=places&callback=initFarmMap`;
    s.async = true;
    s.defer = true;
    document.head.appendChild(s);
})();

/* ── Map initialisation (called by Google callback) ────────────────────── */
let farmMap, farmMarker, geocoder, autocomplete;

window.initFarmMap = function () {
    const defaultCenter = { lat: 39.5, lng: -98.35 };  // center of USA

    farmMap = new google.maps.Map(document.getElementById('farmMap'), {
        zoom            : 4,
        center          : defaultCenter,
        mapTypeId       : 'roadmap',
        mapTypeControl  : true,
        streetViewControl: false,
    });

    geocoder = new google.maps.Geocoder();

    // ── Search autocomplete ───────────────────────────────────────────────
    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('mapSearchInput'),
        { types: ['geocode', 'establishment'] }
    );
    autocomplete.bindTo('bounds', farmMap);

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) return;

        farmMap.setCenter(place.geometry.location);
        farmMap.setZoom(15);
        placeMarker(place.geometry.location);
        fillAddressFields(place.address_components, place.formatted_address);
    });

    // ── Click on map to drop marker ────────────────────────────────────────
    farmMap.addListener('click', function (event) {
        placeMarker(event.latLng);
        reverseGeocode(event.latLng);
    });
};

function placeMarker(location) {
    if (farmMarker) {
        farmMarker.setPosition(location);
    } else {
        farmMarker = new google.maps.Marker({
            position  : location,
            map       : farmMap,
            draggable : true,
            animation : google.maps.Animation.DROP,
        });

        // Allow dragging the marker to fine-tune
        farmMarker.addListener('dragend', function (event) {
            reverseGeocode(event.latLng);
        });
    }

    const lat = location.lat();
    const lng = location.lng();

    // Fill hidden inputs
    document.getElementById('latitude').value  = lat;
    document.getElementById('longitude').value = lng;

    // Fill visible display inputs
    document.getElementById('latDisplay').value = lat.toFixed(7);
    document.getElementById('lngDisplay').value = lng.toFixed(7);
}

function reverseGeocode(latLng) {
    geocoder.geocode({ location: latLng }, function (results, status) {
        if (status === 'OK' && results[0]) {
            fillAddressFields(results[0].address_components, results[0].formatted_address);
        }
    });
}

function fillAddressFields(components, formattedAddress) {
    // Mapped address component types
    const get = (type) => {
        const c = components.find(c => c.types.includes(type));
        return c ? c.long_name : '';
    };
    const getShort = (type) => {
        const c = components.find(c => c.types.includes(type));
        return c ? c.short_name : '';
    };

    document.getElementById('address').value  = formattedAddress || '';
    document.getElementById('city').value     = get('locality') || get('administrative_area_level_2');
    document.getElementById('state').value    = get('administrative_area_level_1');
    document.getElementById('zip_code').value = get('postal_code');
    document.getElementById('country').value  = getShort('country') || 'US';
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Status radio cards ─────────────────────────────────────────────── */
    document.querySelectorAll('.status-card').forEach(card => {
        card.addEventListener('click', function () {
            document.querySelectorAll('.status-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    /* ── Featured toggle card ───────────────────────────────────────────── */
    const featuredCard    = document.getElementById('featuredCard');
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


    /*-----------Owner image preview-------------------------*/

    // Owner Avatar Preview
document.getElementById('owner_avatar').addEventListener('change', function(e) {
    const preview = document.getElementById('ownerAvatarPreview');
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

    /* ── Multi-media preview ────────────────────────────────────────────── */
    document.getElementById('mediaInput').addEventListener('change', function (e) {
        const container = document.getElementById('mediaPreviewRow');
        const files = e.target.files;

        if (files) {
            Array.from(files).forEach(file => {
                const col = document.createElement('div');
                col.className = 'col-4 col-md-3 position-relative';

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        col.innerHTML = `
                            <div class="ratio ratio-1x1 rounded border overflow-hidden">
                                <img src="${e.target.result}" class="object-fit-cover" alt="preview">
                            </div>
                        `;
                        container.appendChild(col);
                    }
                    reader.readAsDataURL(file);
                } else if (file.type.startsWith('video/')) {
                    const url = URL.createObjectURL(file);
                    col.innerHTML = `
                        <div class="ratio ratio-1x1 rounded border overflow-hidden bg-black d-flex align-items-center justify-content-center">
                            <i class="ri-video-line text-white fs-24"></i>
                            <video src="${url}" class="d-none"></video>
                        </div>
                        <small class="text-muted d-block text-truncate small">${file.name}</small>
                    `;
                    container.appendChild(col);
                }
            });
        }
    });

    /* ── Marker color sync ──────────────────────────────────────────────── */
    const picker    = document.getElementById('markerColorPicker');
    const colorText = document.getElementById('marker_color');
    const preview   = document.getElementById('markerPreview');

    picker.addEventListener('input', function () {
        colorText.value     = this.value;
        preview.style.background = this.value;
    });
    colorText.addEventListener('input', function () {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            picker.value = this.value;
            preview.style.background = this.value;
        }
    });

    /* ── Marker icon preview label ──────────────────────────────────────── */
    document.getElementById('marker_icon').addEventListener('input', function () {
        document.getElementById('markerPreviewLabel').textContent = this.value || 'farm_pin';
    });

    /* ── Helpers ────────────────────────────────────────────────────────── */
    function clearErrors() {
        document.querySelectorAll('[id^="error-"]').forEach(el => el.textContent = '');
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    }
    function showFieldErrors(errors) {
        Object.entries(errors).forEach(([field, messages]) => {
            const el = document.getElementById('error-' + field);
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
    document.getElementById('farmForm').addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();
        setLoading(true);

        const fd = new FormData(this);
        fd.set('is_featured', document.getElementById('is_featured').checked ? 1 : 0);

        axios.post('{{ route('admin.farms.store') }}', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(res => {
            Toast.success(res.data.message);
            setTimeout(() => window.location.href = res.data.data.redirect, 800);
        })
        .catch(err => {
            const data = err.response?.data;
            if (data?.errors) {
                showFieldErrors(data.errors);
                if (data.message) Toast.error(data.message);
            } else {
                Toast.fromResponse(data);
            }
        })
        .finally(() => setLoading(false));
    });

});
</script>
@endpush
