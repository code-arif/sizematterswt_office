@extends('layout.master-layout')

@section('title', 'Dashboard')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="h-100">

                        {{-- ── Page Header ───────────────────────────────────────── --}}
                        <div class="row mb-3 pb-1">
                            <div class="col-12">
                                <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                                    <div class="flex-grow-1">
                                        <h4 class="fs-16 mb-1">Good Morning,
                                            {{ auth('admin')->user()?->profile?->name ?? 'Admin' }}!</h4>
                                        <p class="text-muted mb-0">Here's what's happening with your platform today.</p>
                                    </div>
                                    <div class="mt-3 mt-lg-0">
                                        <div class="row g-3 mb-0 align-items-center">
                                            {{-- <div class="col-sm-auto">
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control border-0 minimal-border dash-filter-picker shadow"
                                                        data-provider="flatpickr" data-range-date="true"
                                                        data-date-format="d M, Y"
                                                        data-deafult-date="{{ now()->startOfMonth()->format('d M, Y') }} to {{ now()->format('d M, Y') }}">
                                                    <div class="input-group-text bg-primary border-primary text-white">
                                                        <i class="ri-calendar-2-line"></i>
                                                    </div>
                                                </div>
                                            </div> --}}
                                            <div class="col-auto">
                                                <a href="{{ route('admin.farms.create') }}"
                                                    class="btn btn-soft-success material-shadow-none">
                                                    <i class="ri-add-circle-line align-middle me-1"></i> Add Farm
                                                </a>
                                            </div>
                                            {{-- <div class="col-auto">
                                                <button type="button"
                                                    class="btn btn-soft-info btn-icon waves-effect material-shadow-none waves-light layout-rightside-btn">
                                                    <i class="ri-pulse-line"></i>
                                                </button>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Stat Cards ────────────────────────────────────────── --}}
                        <div class="row">

                            {{-- Total Farms --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total
                                                    Farms</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <h5
                                                    class="fs-14 mb-0 {{ $farmGrowth['up'] ? 'text-success' : 'text-danger' }}">
                                                    <i
                                                        class="ri-arrow-right-{{ $farmGrowth['up'] ? 'up' : 'down' }}-line fs-13 align-middle"></i>
                                                    {{ $farmGrowth['up'] ? '+' : '-' }}{{ $farmGrowth['value'] }}%
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                    <span class="counter-value" data-target="{{ $totalFarms }}">0</span>
                                                </h4>
                                                <a href="{{ route('admin.farms.index') }}"
                                                    class="text-decoration-underline">View all farms</a>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-success-subtle rounded fs-3">
                                                    <i class="bx bx-map-alt text-success"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Total Ranches --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total
                                                    Ranches</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <h5
                                                    class="fs-14 mb-0 {{ $rancheGrowth['up'] ? 'text-success' : 'text-danger' }}">
                                                    <i
                                                        class="ri-arrow-right-{{ $rancheGrowth['up'] ? 'up' : 'down' }}-line fs-13 align-middle"></i>
                                                    {{ $rancheGrowth['up'] ? '+' : '-' }}{{ $rancheGrowth['value'] }}%
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                    <span class="counter-value" data-target="{{ $totalRanches }}">0</span>
                                                </h4>
                                                <a href="#" class="text-decoration-underline">View all ranches</a>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-info-subtle rounded fs-3">
                                                    <i class="bx bx-landscape text-info"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Active Events --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Active
                                                    Events</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <h5
                                                    class="fs-14 mb-0 {{ $eventGrowth['up'] ? 'text-success' : 'text-danger' }}">
                                                    <i
                                                        class="ri-arrow-right-{{ $eventGrowth['up'] ? 'up' : 'down' }}-line fs-13 align-middle"></i>
                                                    {{ $eventGrowth['up'] ? '+' : '-' }}{{ $eventGrowth['value'] }}%
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                    <span class="counter-value" data-target="{{ $totalEvents }}">0</span>
                                                </h4>
                                                <a href="#" class="text-decoration-underline">View all events</a>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-warning-subtle rounded fs-3">
                                                    <i class="bx bx-calendar-event text-warning"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Total Users --}}
                            <div class="col-xl-3 col-md-6">
                                <div class="card card-animate">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Total
                                                    Users</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <h5
                                                    class="fs-14 mb-0 {{ $userGrowth['up'] ? 'text-success' : 'text-danger' }}">
                                                    <i
                                                        class="ri-arrow-right-{{ $userGrowth['up'] ? 'up' : 'down' }}-line fs-13 align-middle"></i>
                                                    {{ $userGrowth['up'] ? '+' : '-' }}{{ $userGrowth['value'] }}%
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-end justify-content-between mt-4">
                                            <div>
                                                <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                                    <span class="counter-value" data-target="{{ $totalUsers }}">0</span>
                                                </h4>
                                                <a href="#" class="text-decoration-underline">See all users</a>
                                            </div>
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-primary-subtle rounded fs-3">
                                                    <i class="bx bx-user-circle text-primary"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- end row -->

                        {{-- ── Growth Chart + Location Bars ─────────────────────── --}}
                        <div class="row">

                            {{-- Monthly Growth Chart --}}
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-header border-0 align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Platform Growth</h4>
                                        <div>
                                            <button type="button"
                                                class="btn btn-soft-secondary material-shadow-none btn-sm chart-range-btn"
                                                data-range="3">3M</button>
                                            <button type="button"
                                                class="btn btn-soft-secondary material-shadow-none btn-sm chart-range-btn"
                                                data-range="6">6M</button>
                                            <button type="button"
                                                class="btn btn-soft-primary material-shadow-none btn-sm chart-range-btn active"
                                                data-range="12">1Y</button>
                                        </div>
                                    </div>

                                    <div class="card-header p-0 border-0 bg-light-subtle">
                                        <div class="row g-0 text-center">
                                            <div class="col-6 col-sm-3">
                                                <div class="p-3 border border-dashed border-start-0">
                                                    <h5 class="mb-1">
                                                        <span class="counter-value"
                                                            data-target="{{ $chartTotalUsers }}">0</span>
                                                    </h5>
                                                    <p class="text-muted mb-0">Users</p>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <div class="p-3 border border-dashed border-start-0">
                                                    <h5 class="mb-1">
                                                        <span class="counter-value"
                                                            data-target="{{ $chartTotalFarms }}">0</span>
                                                    </h5>
                                                    <p class="text-muted mb-0">Farms</p>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <div class="p-3 border border-dashed border-start-0">
                                                    <h5 class="mb-1">
                                                        <span class="counter-value"
                                                            data-target="{{ $chartTotalVisits }}">0</span>
                                                    </h5>
                                                    <p class="text-muted mb-0">Total Visits</p>
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-3">
                                                <div class="p-3 border border-dashed border-start-0 border-end-0">
                                                    <h5 class="mb-1 text-success">
                                                        <span class="counter-value"
                                                            data-target="{{ $chartTotalFavs }}">0</span>
                                                    </h5>
                                                    <p class="text-muted mb-0">Favorites</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-body p-0 pb-2">
                                        <div class="w-100">
                                            <div id="platform_growth_chart" class="apex-charts" dir="ltr"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Top States --}}
                            <div class="col-xl-4">
                                <div class="card card-height-100">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Listings by State</h4>
                                        <div class="flex-shrink-0">
                                            {{-- <button type="button"
                                                class="btn btn-soft-primary material-shadow-none btn-sm">
                                                Export Report
                                            </button> --}}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        {{-- Mini Pie via ApexCharts --}}
                                        <div id="listings_donut_chart" style="height: 200px;" dir="ltr"></div>

                                        <div class="px-2 py-2 mt-1">
                                            @forelse($topStatesFarms as $state)
                                                @php $pct = $maxState > 0 ? round(($state->total / $maxState) * 100) : 0; @endphp
                                                <p class="{{ $loop->first ? 'mb-1' : 'mt-3 mb-1' }}">
                                                    {{ $state->state ?: 'Unknown' }}
                                                    <span class="float-end">{{ $state->total }} farms</span>
                                                </p>
                                                <div class="progress mt-2" style="height: 6px;">
                                                    <div class="progress-bar progress-bar-striped bg-primary"
                                                        role="progressbar" style="width: {{ $pct }}%"
                                                        aria-valuenow="{{ $pct }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-muted text-center mt-4">No state data yet.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- end row -->

                        {{-- ── Recent Farms + Upcoming Events ────────────────────── --}}
                        <div class="row">

                            {{-- Recent Farms --}}
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Recent Farms</h4>
                                        <div class="flex-shrink-0">
                                            <a href="{{ route('admin.farms.index') }}"
                                                class="text-decoration-underline text-muted fs-13">View All</a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                                <tbody>
                                                    @forelse($recentFarms as $farm)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div
                                                                        class="avatar-sm bg-light rounded p-1 me-2 flex-shrink-0">
                                                                        @if ($farm->thumbnail)
                                                                            <img src="{{ asset('storage/' . $farm->thumbnail) }}"
                                                                                alt="{{ $farm->name }}"
                                                                                class="img-fluid d-block rounded"
                                                                                style="width:40px;height:40px;object-fit:cover;" />
                                                                        @else
                                                                            <div class="d-flex align-items-center justify-content-center h-100"
                                                                                style="background:{{ $farm->marker_color }};width:40px;height:40px;border-radius:4px;">
                                                                                <i
                                                                                    class="bx bx-map-alt text-white fs-18"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div>
                                                                        <h5 class="fs-14 my-1">
                                                                            <a href="{{ route('admin.farms.show', $farm->id) }}"
                                                                                class="text-reset">
                                                                                {{ $farm->name }}
                                                                            </a>
                                                                        </h5>
                                                                        <span class="text-muted">{{ $farm->city }},
                                                                            {{ $farm->state }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $map = [
                                                                        'active' => 'bg-success-subtle text-success',
                                                                        'inactive' =>
                                                                            'bg-secondary-subtle text-secondary',
                                                                        'pending' => 'bg-warning-subtle text-warning',
                                                                    ];
                                                                    $cls =
                                                                        $map[$farm->status] ??
                                                                        'bg-secondary-subtle text-secondary';
                                                                @endphp
                                                                <span
                                                                    class="badge {{ $cls }}">{{ ucfirst($farm->status) }}</span>
                                                            </td>
                                                            <td>
                                                                @if ($farm->is_featured)
                                                                    <span
                                                                        class="badge bg-warning-subtle text-warning">Featured</span>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="text-muted">{{ $farm->created_at->format('d M Y') }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex gap-1">
                                                                    <a href="{{ route('admin.farms.show', $farm->id) }}"
                                                                        class="btn btn-sm btn-soft-info" title="View">
                                                                        <i class="ri-eye-fill"></i>
                                                                    </a>
                                                                    <a href="{{ route('admin.farms.edit', $farm->id) }}"
                                                                        class="btn btn-sm btn-soft-primary"
                                                                        title="Edit">
                                                                        <i class="ri-pencil-fill"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted py-4">No
                                                                farms yet.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Upcoming Events --}}
                            <div class="col-xl-6">
                                <div class="card card-height-100">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Upcoming Events</h4>
                                        <div class="flex-shrink-0">
                                            <a href="#" class="text-decoration-underline text-muted fs-13">View
                                                All</a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                                <tbody>
                                                    @forelse($upcomingEvents as $event)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0 me-2">
                                                                        @if ($event->image)
                                                                            <img src="{{ asset('storage/' . $event->image) }}"
                                                                                alt="{{ $event->title }}"
                                                                                class="avatar-sm rounded p-1"
                                                                                style="object-fit:cover;" />
                                                                        @else
                                                                            <div
                                                                                class="avatar-sm bg-danger-subtle rounded d-flex align-items-center justify-content-center">
                                                                                <i
                                                                                    class="bx bx-calendar-event text-danger fs-20"></i>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div>
                                                                        <h5 class="fs-14 my-1 fw-medium">
                                                                            {{ $event->title }}</h5>
                                                                        <span
                                                                            class="text-muted">{{ $event->owner_name ?? '—' }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-muted">{{ $event->city }}</span>
                                                            </td>
                                                            <td>
                                                                <p class="mb-0">{{ $event->start_date->format('d M') }}
                                                                </p>
                                                                <span
                                                                    class="text-muted">{{ $event->start_date->format('Y') }}</span>
                                                            </td>
                                                            <td>
                                                                @if ($event->entry_fee)
                                                                    <span
                                                                        class="text-muted">${{ number_format($event->entry_fee, 2) }}</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-success-subtle text-success">Free</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $emap = [
                                                                        'upcoming' => 'bg-info-subtle text-info',
                                                                        'ongoing' => 'bg-success-subtle text-success',
                                                                    ];
                                                                    $ecls =
                                                                        $emap[$event->status] ??
                                                                        'bg-secondary-subtle text-secondary';
                                                                @endphp
                                                                <span
                                                                    class="badge {{ $ecls }}">{{ ucfirst($event->status) }}</span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted py-4">No
                                                                upcoming events.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- end row -->

                        {{-- ── Type Distribution + Recent Users ─────────────────── --}}
                        <div class="row">

                            {{-- Listing Type Distribution --}}
                            <div class="col-xl-4">
                                <div class="card card-height-100">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Listing Distribution</h4>
                                    </div>
                                    <div class="card-body">
                                        <div id="listing_type_chart" class="apex-charts" dir="ltr"
                                            style="height: 269px;"></div>

                                        <div class="px-2 py-2 mt-1">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><i class="bx bx-circle text-success me-1"></i>Farms</span>
                                                <span class="fw-semibold">{{ $pieFarms }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><i class="bx bx-circle text-warning me-1"></i>Ranches</span>
                                                <span class="fw-semibold">{{ $pieRanches }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="bx bx-circle text-danger me-1"></i>Events</span>
                                                <span class="fw-semibold">{{ $pieEvents }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Recent User Registrations --}}
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-header align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Recent Registrations</h4>
                                        <div class="flex-shrink-0">
                                            {{-- <button type="button" class="btn btn-soft-info btn-sm material-shadow-none">
                                                <i class="ri-file-list-3-line align-middle"></i> Export
                                            </button> --}}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-card">
                                            <table
                                                class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                                <thead class="text-muted table-light">
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Username</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Joined</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($recentUsers as $user)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0 me-2">
                                                                        <div
                                                                            class="avatar-xs rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center">
                                                                            <span class="text-primary fw-semibold fs-12">
                                                                                {{ strtoupper(substr($user->profile?->name ?? 'U', 0, 1)) }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        {{ $user->profile?->name ?? '—' }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-muted">{{ $user -> profile?->username ?? '—' }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="text-muted">{{ $user->email }}</span>
                                                            </td>
                                                            <td>
                                                                @php
                                                                    $umap = [
                                                                        'active' => 'bg-success-subtle text-success',
                                                                        'inactive' =>
                                                                            'bg-secondary-subtle text-secondary',
                                                                        'banned' => 'bg-danger-subtle text-danger',
                                                                    ];
                                                                    $ucls =
                                                                        $umap[$user->status] ??
                                                                        'bg-secondary-subtle text-secondary';
                                                                @endphp
                                                                <span
                                                                    class="badge {{ $ucls }}">{{ ucfirst($user->status) }}</span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="text-muted">{{ $user->created_at->format('d M Y') }}</span>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted py-4">No
                                                                users yet.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div><!-- end row -->

                    </div><!-- end .h-100 -->
                </div><!-- end col -->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            'use strict';

            // ── Chart data from PHP ───────────────────────────────────────────────
            var chartLabels = {!! $chartLabels !!};
            var chartUsers = {!! $chartUsers !!};
            var chartFarms = {!! $chartFarms !!};
            var chartEvents = {!! $chartEvents !!};

            // ── Resolve CSS variable colors safely ───────────────────────────────
            function cssVar(name) {
                return getComputedStyle(document.documentElement)
                    .getPropertyValue(name).trim() || '#405189';
            }

            // ── 1. Platform Growth (Area chart) ──────────────────────────────────
            var growthOptions = {
                series: [{
                        name: 'New Users',
                        data: chartUsers
                    },
                    {
                        name: 'New Farms',
                        data: chartFarms
                    },
                    {
                        name: 'New Events',
                        data: chartEvents
                    },
                ],
                chart: {
                    type: 'area',
                    height: 330,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1
                    }
                },
                xaxis: {
                    categories: chartLabels,
                    labels: {
                        rotate: -30,
                        style: {
                            fontSize: '11px'
                        }
                    },
                },
                yaxis: {
                    labels: {
                        formatter: function(v) {
                            return Math.round(v);
                        }
                    }
                },
                colors: [cssVar('--vz-primary'), cssVar('--vz-success'), cssVar('--vz-danger')],
                legend: {
                    position: 'top',
                    horizontalAlign: 'right'
                },
                tooltip: {
                    shared: true,
                    intersect: false
                },
                grid: {
                    borderColor: '#f1f1f1'
                },
            };

            var growthChart = new ApexCharts(document.querySelector('#platform_growth_chart'), growthOptions);
            growthChart.render();

            // Range buttons — slice last N months
            document.querySelectorAll('.chart-range-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.chart-range-btn').forEach(function(b) {
                        b.classList.remove('btn-soft-primary');
                        b.classList.add('btn-soft-secondary');
                    });
                    this.classList.remove('btn-soft-secondary');
                    this.classList.add('btn-soft-primary');

                    var n = parseInt(this.dataset.range);
                    growthChart.updateOptions({
                        xaxis: {
                            categories: chartLabels.slice(-n)
                        },
                    });
                    growthChart.updateSeries([{
                            name: 'New Users',
                            data: chartUsers.slice(-n)
                        },
                        {
                            name: 'New Farms',
                            data: chartFarms.slice(-n)
                        },
                        {
                            name: 'New Events',
                            data: chartEvents.slice(-n)
                        },
                    ]);
                });
            });

            // ── 2. Listing Type Donut ─────────────────────────────────────────────
            var donutOptions = {
                series: [{{ $pieFarms }}, {{ $pieRanches }}, {{ $pieEvents }}],
                chart: {
                    type: 'donut',
                    height: 269
                },
                labels: ['Farms', 'Ranches', 'Events'],
                colors: [cssVar('--vz-success'), cssVar('--vz-warning'), cssVar('--vz-danger')],
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: true
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    formatter: function(w) {
                                        return w.globals.seriesTotals.reduce(function(a, b) {
                                            return a + b;
                                        }, 0);
                                    }
                                }
                            }
                        }
                    }
                },
            };

            var donutChart = new ApexCharts(document.querySelector('#listing_type_chart'), donutOptions);
            donutChart.render();

            // ── 3. State Donut (top widget) ───────────────────────────────────────
            @php
                $stateLabels = $topStatesFarms->pluck('state')->map(fn($s) => $s ?: 'Unknown')->toJson();
                $stateData = $topStatesFarms->pluck('total')->toJson();
            @endphp
            var stateLabels = {!! $stateLabels !!};
            var stateData = {!! $stateData !!};

            if (stateData.length > 0) {
                var stateDonutOptions = {
                    series: stateData,
                    chart: {
                        type: 'donut',
                        height: 200
                    },
                    labels: stateLabels,
                    colors: ['#405189', '#0ab39c', '#f7b84b', '#f06548', '#299cdb'],
                    legend: {
                        position: 'bottom',
                        fontSize: '11px'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%'
                            }
                        }
                    },
                };
                new ApexCharts(document.querySelector('#listings_donut_chart'), stateDonutOptions).render();
            } else {
                document.querySelector('#listings_donut_chart').innerHTML =
                    '<p class="text-center text-muted py-4">No state data yet.</p>';
            }

        })();
    </script>
@endpush
