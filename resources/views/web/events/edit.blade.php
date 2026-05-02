@extends('layout.master-layout')

@section('title', 'Edit Event - ' . $event->title)

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Edit Event</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form id="eventForm" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- ── Left column ─────────────────────────────────────────── --}}
                    <div class="col-lg-8">

                        {{-- Owner Information Section --}}
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Owner Information</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            {{-- Owner Name --}}
            <div class="col-lg-6">
                <label for="owner_name" class="form-label">Owner Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="owner_name" name="owner_name"
                    value="{{ old('owner_name', $event->owner_name) }}" placeholder="Enter owner name">
                <div class="text-danger small mt-1" id="error-owner_name"></div>
            </div>

            {{-- Owner Phone --}}
            <div class="col-lg-6">
                <label for="owner_phone" class="form-label">Owner Phone</label>
                <input type="text" class="form-control" id="owner_phone" name="owner_phone"
                    value="{{ old('owner_phone', $event->owner_phone) }}" placeholder="Enter owner phone">
                <div class="text-danger small mt-1" id="error-owner_phone"></div>
            </div>

            {{-- Owner Address --}}
            <div class="col-12">
                <label for="owner_address" class="form-label">Owner Address</label>
                <input type="text" class="form-control" id="owner_address" name="owner_address"
                    value="{{ old('owner_address', $event->owner_address) }}" placeholder="Enter owner address">
                <div class="text-danger small mt-1" id="error-owner_address"></div>
            </div>

            {{-- Owner Avatar --}}
            <div class="col-12">
                <label for="owner_avatar" class="form-label">Owner Avatar</label>
                <div class="d-flex align-items-start gap-3">
                    {{-- Database e image thakle seta dekhabe, na thakle default dummy image --}}
                    <img id="ownerAvatarPreview"
                        src="{{ $event->owner_avatar ? asset( $event->owner_avatar) : asset('admin/assets/images/users/user-dummy-img.jpg') }}"
                        class="rounded-circle avatar-lg img-thumbnail"
                        style="width: 80px; height: 80px; object-fit: cover;" alt="Owner Avatar">

                    <div class="flex-grow-1">
                        <input type="file" class="form-control" id="owner_avatar" name="owner_avatar"
                            accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">Leave blank to keep current avatar. JPG, PNG, WEBP — max 2 MB</small>
                        <div class="text-danger small mt-1" id="error-owner_avatar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Basic Information</h5></div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-12">
                                        <label for="title" class="form-label">Event Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ old('title', $event->title) }}">
                                        <div class="text-danger small mt-1" id="error-title"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description"
                                            rows="3" maxlength="2000">{{ old('description', $event->description) }}</textarea>
                                        <div class="d-flex justify-content-between mt-1">
                                            <div class="text-danger small" id="error-description"></div>
                                            <small class="text-muted"><span id="descCount">{{ strlen($event->description ?? '') }}</span>/2000</small>
                                        </div>
                                    </div>

                                    {{-- Date Range --}}
                                    <div class="col-lg-6">
                                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                            value="{{ old('start_date', $event->start_date?->format('Y-m-d\TH:i')) }}">
                                        <div class="text-danger small mt-1" id="error-start_date"></div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                            value="{{ old('end_date', $event->end_date?->format('Y-m-d\TH:i')) }}">
                                        <div class="text-danger small mt-1" id="error-end_date"></div>
                                    </div>

                                    {{-- Entry Fee & Capacity --}}
                                    <div class="col-lg-6">
                                        <label for="entry_fee" class="form-label">Entry Fee
                                            <small class="text-muted">(leave blank if free)</small>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="entry_fee" name="entry_fee"
                                                value="{{ old('entry_fee', $event->entry_fee) }}" min="0" step="0.01">
                                        </div>
                                        <div class="text-danger small mt-1" id="error-entry_fee"></div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="capacity" class="form-label">Capacity
                                            <small class="text-muted">(max attendees)</small>
                                        </label>
                                        <input type="number" class="form-control" id="capacity" name="capacity"
                                            value="{{ old('capacity', $event->capacity) }}" min="1" step="1">
                                        <div class="text-danger small mt-1" id="error-capacity"></div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ old('phone', $event->phone) }}">
                                        <div class="text-danger small mt-1" id="error-phone"></div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $event->email) }}">
                                        <div class="text-danger small mt-1" id="error-email"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" class="form-control" id="website" name="website"
                                            value="{{ old('website', $event->website) }}">
                                        <div class="text-danger small mt-1" id="error-website"></div>
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
                                            <span class="input-group-text bg-light"><i class="ri-map-pin-line text-muted"></i></span>
                                            <input type="text" id="mapSearchInput" class="form-control" placeholder="Search address or place name…">
                                        </div>
                                        <small class="text-muted">Search or click the map to update the pin.</small>
                                    </div>

                                    <div class="col-12">
                                        <div id="eventMap" style="width:100%;height:400px;border-radius:8px;border:1px solid #dee2e6;"></div>
                                    </div>

                                    <input type="hidden" id="latitude"  name="latitude"  value="{{ old('latitude',  $event->latitude) }}">
                                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $event->longitude) }}">

                                    <div class="col-12">
                                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ old('address', $event->address) }}">
                                        <div class="text-danger small mt-1" id="error-address"></div>
                                    </div>

                                    <div class="col-lg-4">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city"
                                            value="{{ old('city', $event->city) }}">
                                    </div>
                                    <div class="col-lg-4">
                                        <label for="state" class="form-label">State</label>
                                        <input type="text" class="form-control" id="state" name="state"
                                            value="{{ old('state', $event->state) }}">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="zip_code" class="form-label">ZIP</label>
                                        <input type="text" class="form-control" id="zip_code" name="zip_code"
                                            value="{{ old('zip_code', $event->zip_code) }}">
                                    </div>
                                    <div class="col-lg-2">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" class="form-control" id="country" name="country"
                                            value="{{ old('country', $event->country) }}">
                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label">Latitude</label>
                                        <input type="text" class="form-control bg-light" id="latDisplay" readonly
                                            value="{{ $event->latitude }}">
                                        <div class="text-danger small mt-1" id="error-latitude"></div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">Longitude</label>
                                        <input type="text" class="form-control bg-light" id="lngDisplay" readonly
                                            value="{{ $event->longitude }}">
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

                                            <label class="status-card upcoming-card {{ $event->status === 'upcoming' ? 'selected' : '' }}" for="status_upcoming">
                                                <input type="radio" name="status" id="status_upcoming" value="upcoming"
                                                    @checked($event->status === 'upcoming') class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon"><i class="ri-calendar-line"></i></span>
                                                    <div><p class="mb-0 fw-semibold">Upcoming</p><small>Scheduled and not yet started</small></div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                            <label class="status-card ongoing-card {{ $event->status === 'ongoing' ? 'selected' : '' }}" for="status_ongoing">
                                                <input type="radio" name="status" id="status_ongoing" value="ongoing"
                                                    @checked($event->status === 'ongoing') class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon"><i class="ri-live-line"></i></span>
                                                    <div><p class="mb-0 fw-semibold">Ongoing</p><small>Currently in progress</small></div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                            <label class="status-card completed-card {{ $event->status === 'completed' ? 'selected' : '' }}" for="status_completed">
                                                <input type="radio" name="status" id="status_completed" value="completed"
                                                    @checked($event->status === 'completed') class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon"><i class="ri-checkbox-circle-line"></i></span>
                                                    <div><p class="mb-0 fw-semibold">Completed</p><small>Event has ended</small></div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                            <label class="status-card cancelled-card {{ $event->status === 'cancelled' ? 'selected' : '' }}" for="status_cancelled">
                                                <input type="radio" name="status" id="status_cancelled" value="cancelled"
                                                    @checked($event->status === 'cancelled') class="d-none">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="status-icon"><i class="ri-close-circle-line"></i></span>
                                                    <div><p class="mb-0 fw-semibold">Cancelled</p><small>Event will not take place</small></div>
                                                </div>
                                                <i class="ri-check-line check-mark"></i>
                                            </label>

                                        </div>
                                        <div class="text-danger small mt-1" id="error-status"></div>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="hstack gap-2">
                                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                                <span id="submitBtnText"><i class="ri-save-line me-1"></i> Update Event</span>
                                                <span id="submitBtnSpinner" class="d-none">
                                                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                                    Updating…
                                                </span>
                                            </button>
                                            <a href="{{ route('admin.events.index') }}" class="btn btn-light">Cancel</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Event Gallery --}}
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1">Event Gallery</h5>
                                <span class="badge bg-info-subtle text-info">{{ $event->media->count() }} Files</span>
                            </div>
                            <div class="card-body">
                                {{-- Existing Media Grid --}}
                                @if($event->media->isNotEmpty())
                                    <div class="row g-3 mb-4">
                                        @foreach($event->media as $media)
                                            <div class="col-lg-3 col-md-4 col-6 media-item-wrapper" id="media-item-{{ $media->id }}">
                                                <div class="position-relative rounded overflow-hidden shadow-sm border" style="aspect-ratio:16/9;">
                                                    @if($media->media_type === 'video')
                                                        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-dark text-white">
                                                            <i class="ri-video-line fs-24"></i>
                                                            <small class="text-truncate px-2 w-100 text-center">{{ $media->file_name }}</small>
                                                        </div>
                                                    @else
                                                        <img src="{{ $media->url }}" class="w-100 h-100" style="object-fit:cover;">
                                                    @endif

                                                    <div class="position-absolute top-0 end-0 p-1">
                                                        <button type="button" class="btn btn-sm btn-danger remove-media-btn"
                                                            data-id="{{ $media->id }}" title="Delete Media">
                                                            <i class="ri-delete-bin-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <hr class="text-muted opacity-25">
                                @endif

                                {{-- New Media Upload --}}
                                <div class="mt-3">
                                    <label for="media" class="form-label">Add New Media (Images/Videos)</label>
                                    <input type="file" class="form-control" id="media" name="media[]" multiple
                                        accept="image/*,video/mp4">
                                    <small class="text-muted">Max 10MB per file. New files will be appended to the gallery.</small>
                                    <div class="text-danger small mt-1" id="error-media"></div>
                                </div>

                                {{-- Preview Container for new files --}}
                                <div id="mediaPreviewContainer" class="row g-3 mt-1"></div>
                            </div>
                        </div>

                        {{-- Event Image --}}
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Event Thumbnail / Cover</h5></div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <img id="imagePreview"
                                        src="{{ $event->image ? asset( $event->image) : asset('admin/assets/images/default/event-placeholder.jpg') }}"
                                        class="img-fluid rounded" style="max-height:180px;object-fit:cover;width:100%;" alt="">
                                </div>
                                <label for="image" class="form-label">Change Cover Image</label>
                                <input type="file" class="form-control" id="image" name="image"
                                    accept="image/jpeg,image/png,image/webp">
                                <small class="text-muted">Leave blank to keep existing. JPG, PNG, WEBP — max 2 MB.</small>
                                <div class="text-danger small mt-1" id="error-image"></div>
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
    #eventMap { cursor: crosshair; }
    .pac-container { z-index: 9999 !important; }
    .status-card { display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-radius:8px;border:1.5px solid #e9ebec;cursor:pointer;transition:all .2s ease;background:#fff;user-select:none; }
    .status-card:hover { border-color:#c8cdd5; }
    .status-card .status-icon { width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0; }
    .status-card small { color:#878a99;font-size:12px; } .status-card p { font-size:14px;color:#212529; }
    .check-mark { font-size:18px;opacity:0;transition:opacity .15s;flex-shrink:0; }
    .upcoming-card .status-icon  { background:#d1ecf1;color:#0dcaf0; } .upcoming-card.selected  { border-color:#0dcaf0;background:#f0fbfd; } .upcoming-card.selected .check-mark  { opacity:1;color:#0dcaf0; }
    .ongoing-card .status-icon   { background:#d1f0ea;color:#0ab39c; } .ongoing-card.selected   { border-color:#0ab39c;background:#f0fbf9; } .ongoing-card.selected .check-mark   { opacity:1;color:#0ab39c; }
    .completed-card .status-icon { background:#e9ebec;color:#878a99; } .completed-card.selected { border-color:#878a99;background:#f8f9fa; } .completed-card.selected .check-mark { opacity:1;color:#878a99; }
    .cancelled-card .status-icon { background:#fde8e8;color:#f06548; } .cancelled-card.selected { border-color:#f06548;background:#fff5f3; } .cancelled-card.selected .check-mark { opacity:1;color:#f06548; }
</style>
@endpush

@push('scripts')
<script>
    window.__GOOGLE_MAPS_KEY = '{{ env('GOOGLE_MAPS_API_KEY') }}';
    window.__EVENT_LAT       = {{ $event->latitude }};
    window.__EVENT_LNG       = {{ $event->longitude }};
</script>
<script>
(function () {
    const s = document.createElement('script');
    s.src = `https://maps.googleapis.com/maps/api/js?key=${window.__GOOGLE_MAPS_KEY}&libraries=places&callback=initEventMap`;
    s.async = true; s.defer = true;
    document.head.appendChild(s);
})();

let eventMap, eventMarker, geocoder, autocomplete;

window.initEventMap = function () {
    const center = { lat: window.__EVENT_LAT, lng: window.__EVENT_LNG };
    eventMap = new google.maps.Map(document.getElementById('eventMap'), {
        zoom: 14, center, mapTypeId: 'roadmap', streetViewControl: false,
    });
    geocoder = new google.maps.Geocoder();

    eventMarker = new google.maps.Marker({
        position: center, map: eventMap, draggable: true,
        animation: google.maps.Animation.DROP,
    });
    eventMarker.addListener('dragend', function (e) {
        reverseGeocode(e.latLng);
        updateLatLng(e.latLng);
    });

    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('mapSearchInput'), { types: ['geocode', 'establishment'] }
    );
    autocomplete.bindTo('bounds', eventMap);
    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (!place.geometry?.location) return;
        eventMap.setCenter(place.geometry.location); eventMap.setZoom(15);
        placeMarker(place.geometry.location);
        fillAddressFields(place.address_components, place.formatted_address);
    });

    eventMap.addListener('click', function (event) {
        placeMarker(event.latLng);
        reverseGeocode(event.latLng);
    });
};

function placeMarker(location) { eventMarker.setPosition(location); updateLatLng(location); }

function updateLatLng(location) {
    const lat = location.lat(), lng = location.lng();
    document.getElementById('latitude').value   = lat;
    document.getElementById('longitude').value  = lng;
    document.getElementById('latDisplay').value = lat.toFixed(7);
    document.getElementById('lngDisplay').value = lng.toFixed(7);
}

function reverseGeocode(latLng) {
    geocoder.geocode({ location: latLng }, function (results, status) {
        if (status === 'OK' && results[0])
            fillAddressFields(results[0].address_components, results[0].formatted_address);
    });
}

function fillAddressFields(components, formattedAddress) {
    const get      = type => components.find(c => c.types.includes(type))?.long_name  || '';
    const getShort = type => components.find(c => c.types.includes(type))?.short_name || '';
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


    // Owner Avatar Preview logic
    document.getElementById('owner_avatar').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('ownerAvatarPreview').setAttribute('src', event.target.result);
            }
            reader.readAsDataURL(file);
        }
    });


    /* ── Char counter ───────────────────────────────────────────────────── */
    document.getElementById('description').addEventListener('input', function () {
        document.getElementById('descCount').textContent = this.value.length;
    });

    /* ── Image preview ──────────────────────────────────────────────────── */
    document.getElementById('image').addEventListener('change', function () {
        const file = this.files[0]; if (!file) return;
        const reader = new FileReader();
        reader.onload = e => document.getElementById('imagePreview').src = e.target.result;
        reader.readAsDataURL(file);
    });

    /* ── Gallery Management ────────────────────────────────────────────── */

    // Preview new files
    document.getElementById('media').addEventListener('change', function () {
        const container = document.getElementById('mediaPreviewContainer');
        container.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const col = document.createElement('div');
            col.className = 'col-lg-3 col-md-4 col-6';
            const wrapper = document.createElement('div');
            wrapper.className = 'position-relative rounded overflow-hidden shadow-sm border';
            wrapper.style.aspectRatio = '16/9';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'w-100 h-100'; img.style.objectFit = 'cover';
                const reader = new FileReader();
                reader.onload = e => img.src = e.target.result;
                reader.readAsDataURL(file);
                wrapper.appendChild(img);
            } else {
                wrapper.style.background = '#1a1a1a';
                wrapper.innerHTML = `<div class="d-flex flex-column align-items-center justify-content-center h-100 text-white">
                    <i class="ri-video-line fs-24 mb-1"></i><small class="px-2 text-center text-truncate w-100">${file.name}</small>
                </div><span class="position-absolute top-0 end-0 m-1 badge bg-primary">New Video</span>`;
            }
            col.appendChild(wrapper);
            container.appendChild(col);
        });
    });

    // Delete existing media
    document.querySelectorAll('.remove-media-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const mediaId = this.dataset.id;
            Alert.confirm('This file will be permanently deleted.', {
                title: 'Delete Media File?', type: 'danger', confirmText: 'Yes, delete it'
            }).then(confirmed => {
                if (!confirmed) return;

                const url = "{{ route('admin.events.media.delete', [$event->id, ':mediaId']) }}"
                    .replace(':mediaId', mediaId);

                axios.delete(url, { data: { _token: '{{ csrf_token() }}' } })
                    .then(res => {
                        Toast.success(res.data.message);
                        document.getElementById(`media-item-${mediaId}`).remove();
                    })
                    .catch(err => Toast.fromResponse(err.response?.data));
            });
        });
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
            if (el)    el.textContent = messages[0];
            if (input) input.classList.add('is-invalid');
        });
    }
    function setLoading(state) {
        document.getElementById('submitBtn').disabled = state;
        document.getElementById('submitBtnText').classList.toggle('d-none', state);
        document.getElementById('submitBtnSpinner').classList.toggle('d-none', !state);
    }

    /* ── Form submit ────────────────────────────────────────────────────── */
    document.getElementById('eventForm').addEventListener('submit', function (e) {
        e.preventDefault(); clearErrors(); setLoading(true);

        const fd = new FormData(this);

        axios.post('{{ route('admin.events.update', $event->id) }}', fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
        .then(res => {
            Toast.success(res.data.message);
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
