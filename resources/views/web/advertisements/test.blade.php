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
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.advertisements.index') }}">Advertisements</a></li>
                                <li class="breadcrumb-item active">Create</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <form id="adForm" enctype="multipart/form-data">
                @csrf
                <div class="row">

                    {{-- Left Column --}}
                    <div class="col-xl-8">

                        {{-- Basic Info --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Ad Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control"
                                        placeholder="e.g. Visit Green Valley Farm" maxlength="150">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Subtitle</label>
                                    <input type="text" name="subtitle" class="form-control"
                                        placeholder="Short description or tagline" maxlength="255">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">CTA Button Label</label>
                                    <input type="text" name="cta_label" class="form-control" value="View Details"
                                        maxlength="50">
                                    <div class="form-text">Text on the button shown in the ad banner.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Banner Image --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Banner Image <span class="text-danger">*</span></h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                                    <div class="form-text">JPG, PNG, WEBP — max 2 MB. Recommended: 600×200 px (3:1 ratio)
                                    </div>
                                </div>
                                <div id="imagePreviewWrap" class="d-none mt-3">
                                    <p class="text-muted mb-1 fs-13">Preview:</p>
                                    <img id="imagePreview" src="" class="rounded border"
                                        style="max-width:100%;height:160px;object-fit:cover;" />
                                </div>
                            </div>
                        </div>

                        {{-- Trigger Location Map --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Trigger Location <span class="text-danger">*</span></h5>
                                <small class="text-muted">Click on the map to set where this ad will appear</small>
                            </div>
                            <div class="card-body">
                                {{-- Search box --}}
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" id="locationSearch" class="form-control"
                                            placeholder="Search address or place...">
                                        <button class="btn btn-outline-primary" type="button" id="searchBtn">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </div>
                                </div>
                                {{-- Map --}}
                                <div id="triggerMap" style="height:380px;border-radius:8px;border:1px solid #dee2e6;"></div>
                                {{-- Hidden lat/lng --}}
                                <input type="hidden" name="trigger_latitude" id="triggerLat">
                                <input type="hidden" name="trigger_longitude" id="triggerLng">
                                {{-- Coords display --}}
                                <div id="coordsDisplay" class="mt-2 text-muted fs-13 d-none">
                                    <i class="ri-map-pin-2-fill text-danger me-1"></i>
                                    <span id="coordsText"></span>
                                </div>

                                <div class="mb-3 mt-3">
                                    <label class="form-label">Trigger Radius</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="range" id="radiusSlider" name="radius_meters"
                                            class="form-range flex-grow-1" min="100" max="10000" step="100" value="1000">
                                        <span id="radiusLabel" class="badge bg-primary-subtle text-primary fs-13 fw-medium"
                                            style="min-width:70px;">1.0 km</span>
                                    </div>
                                    <div class="form-text">How far from the trigger point users will see this ad.</div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Right Column --}}
                    <div class="col-xl-4">

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
                                </div>
                                <div class="mb-3" id="linkedIdWrap" style="display:none;">
                                    <label class="form-label">Select <span id="linkedTypeLabel">Item</span></label>
                                    <select name="linked_id" id="linkedId" class="form-select">
                                        <option value="">— Select —</option>
                                    </select>
                                </div>

                                {{-- Auto-fill trigger from entity --}}
                                <div id="autoFillHint" class="alert alert-info d-none fs-13 p-2">
                                    <i class="ri-information-line me-1"></i>
                                    Select an entity to auto-fill the trigger location from its coordinates.
                                </div>
                                <button type="button" id="autoFillBtn" class="btn btn-soft-info btn-sm d-none w-100">
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
                                    <input type="text" name="starts_at" class="form-control" data-provider="flatpickr"
                                        data-date-format="Y-m-d" placeholder="Always on (leave blank)">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="text" name="ends_at" class="form-control" data-provider="flatpickr"
                                        data-date-format="Y-m-d" placeholder="No expiry (leave blank)">
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Status</h5>
                            </div>
                            <div class="card-body">
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="card">
                            <div class="card-body d-grid gap-2">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="ri-save-line me-1"></i> Create Advertisement
                                </button>
                                <a href="{{ route('admin.advertisements.index') }}" class="btn btn-light">
                                    Cancel
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Google Maps --}}

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap"
        async defer>
        </script>

    {{-- Entity data for JS --}}
    <script>
        var FARMS = @json($farms);
        var RANCHES = @json($ranches);
        var EVENTS = @json($events);
        var ENTITY_MAP = { farm: FARMS, ranch: RANCHES, event: EVENTS };
    </script>


    <script>
        // ── Google Maps init ──────────────────────────────────────────────────────────
        var map, marker, circle;

        function initMap() {
            map = new google.maps.Map(document.getElementById('triggerMap'), {
                center: { lat: 39.5, lng: -98.35 }, // USA center default
                zoom: 4,
            });

            map.addListener('click', function (e) {
                placeMarker(e.latLng.lat(), e.latLng.lng());
            });

            // Places autocomplete on search
            var input = document.getElementById('locationSearch');
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                if (place.geometry) {
                    map.setCenter(place.geometry.location);
                    map.setZoom(13);
                    placeMarker(place.geometry.location.lat(), place.geometry.location.lng());
                }
            });
        }

        function placeMarker(lat, lng) {
            document.getElementById('triggerLat').value = lat;
            document.getElementById('triggerLng').value = lng;

            var pos = { lat: lat, lng: lng };

            if (marker) {
                marker.setPosition(pos);
            } else {
                marker = new google.maps.Marker({ position: pos, map: map, draggable: true });
                marker.addListener('dragend', function (e) {
                    placeMarker(e.latLng.lat(), e.latLng.lng());
                });
            }

            updateCircle(pos);

            document.getElementById('coordsDisplay').classList.remove('d-none');
            document.getElementById('coordsText').textContent =
                lat.toFixed(6) + ', ' + lng.toFixed(6);
        }

        function updateCircle(center) {
            var radius = parseInt(document.getElementById('radiusSlider').value);
            if (circle) {
                circle.setCenter(center);
                circle.setRadius(radius);
            } else {
                circle = new google.maps.Circle({
                    map: map,
                    center: center,
                    radius: radius,
                    fillColor: '#405189',
                    fillOpacity: 0.15,
                    strokeColor: '#405189',
                    strokeOpacity: 0.6,
                    strokeWeight: 1,
                });
            }
        }

        // ── Radius slider ─────────────────────────────────────────────────────────────
        $(function () {
            $('#radiusSlider').on('input', function () {
                var v = parseInt(this.value);
                var label = v >= 1000 ? (v / 1000).toFixed(1) + ' km' : v + ' m';
                $('#radiusLabel').text(label);
                if (circle) circle.setRadius(v);
            });

            // ── Image preview ─────────────────────────────────────────────────────
            $('#imageInput').on('change', function () {
                var file = this.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreviewWrap').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            });

            // ── Linked type selector ──────────────────────────────────────────────
            $('#linkedType').on('change', function () {
                var type = this.value;
                if (!type) {
                    $('#linkedIdWrap').hide();
                    $('#autoFillHint').addClass('d-none');
                    $('#autoFillBtn').addClass('d-none');
                    return;
                }

                $('#linkedTypeLabel').text(type.charAt(0).toUpperCase() + type.slice(1));
                var entities = ENTITY_MAP[type] || [];
                var opts = '<option value="">— Select —</option>';
                entities.forEach(function (e) {
                    opts += '<option value="' + e.id + '" data-lat="' + e.lat + '" data-lng="' + e.lng + '">' + e.name + '</option>';
                });
                $('#linkedId').html(opts);
                $('#linkedIdWrap').show();
                $('#autoFillHint').removeClass('d-none');
            });

            $('#linkedId').on('change', function () {
                var opt = $(this).find(':selected');
                var lat = opt.data('lat'), lng = opt.data('lng');
                if (lat && lng) $('#autoFillBtn').removeClass('d-none');
                else $('#autoFillBtn').addClass('d-none');
            });

            $('#autoFillBtn').on('click', function () {
                var opt = $('#linkedId').find(':selected');
                var lat = parseFloat(opt.data('lat'));
                var lng = parseFloat(opt.data('lng'));
                if (!lat || !lng) return;
                map.setCenter({ lat: lat, lng: lng });
                map.setZoom(13);
                placeMarker(lat, lng);
            });

            // ── Form submit ───────────────────────────────────────────────────────
            $('#adForm').on('submit', function (e) {
                e.preventDefault();

                if (!$('#triggerLat').val()) {
                    toastr.error('Please click the map to set a trigger location.');
                    return;
                }

                var btn = $('#submitBtn').prop('disabled', true).text('Saving...');
                var data = new FormData(this);

                $.ajax({
                    url: '{{ route("admin.advertisements.store") }}',
                    method: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function (res) {
                        toastr.success(res.message);
                        setTimeout(function () {
                            window.location.href = res.data.redirect;
                        }, 800);
                    },
                    error: function (xhr) {
                        btn.prop('disabled', false).text('Create Advertisement');
                        var errors = xhr.responseJSON?.errors ?? {};
                        Object.values(errors).flat().forEach(function (msg) {
                            toastr.error(msg);
                        });
                    }
                });
            });
        });
    </script>
@endpush
