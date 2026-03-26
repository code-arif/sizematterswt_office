@extends('layout.master-layout')

@section('title', 'Events')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Events</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Events</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">All Events</h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-bottom me-1"></i> Add Event
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="eventsTable"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SR No.</th>
                                        <th>Title</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Entry Fee</th>
                                        <th>Status</th>
                                        <th>Added By</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const table = $('#eventsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.events.datatable') }}',
                    type: 'GET'
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        render: (d, t, r) => `<div class="d-flex align-items-center gap-2">
                            ${r.image
                                ? `<img src="/storage/${r.image}" class="rounded avatar-xs object-fit-cover" alt="">`
                                : `<div class="avatar-xs bg-light rounded d-flex align-items-center justify-content-center">
                                       <i class="ri-calendar-event-line text-muted fs-16"></i>
                                   </div>`}
                            <span class="fw-medium">${d}</span>
                        </div>`
                    },
                    {
                        data: 'address'
                    },
                    {
                        data: 'phone',
                        defaultContent: '—'
                    },
                    {
                        data: 'start_date',
                        render: d => d ? new Date(d).toLocaleDateString('en-US', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : '—'
                    },
                    {
                        data: 'end_date',
                        render: d => d ? new Date(d).toLocaleDateString('en-US', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        }) : '—'
                    },
                    {
                        data: 'entry_fee_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'admin_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        render: d => new Date(d).toLocaleDateString('en-US', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        })
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                order: [[9, 'desc']],
                pageLength: 10,
                language: {
                    processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"></div>'
                },
            });

            /* ── Delete ──────────────────────────────────────────────────────────── */
            $('#eventsTable').on('click', '.delete-event', function () {
                const id = this.dataset.id;
                Alert.confirm('This event will be permanently removed.', {
                    title: 'Delete Event?',
                    type: 'danger',
                    confirmText: 'Yes, delete it',
                }).then(confirmed => {
                    if (!confirmed) return;
                    axios.delete(`/admin/events/${id}`, {
                            data: {
                                _token: document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(res => {
                            Toast.success(res.data.message);
                            table.ajax.reload(null, false);
                        })
                        .catch(err => Toast.fromResponse(err.response?.data));
                });
            });

            /* ── Toggle status ───────────────────────────────────────────────────── */
            $('#eventsTable').on('click', '.toggle-status', function () {
                const id = this.dataset.id;
                axios.patch(`/admin/events/${id}/toggle-status`, {
                        _token: document.querySelector('meta[name="csrf-token"]').content
                    })
                    .then(res => {
                        Toast.success(res.data.message);
                        table.ajax.reload(null, false);
                    })
                    .catch(err => Toast.fromResponse(err.response?.data));
            });

        });
    </script>
@endpush
