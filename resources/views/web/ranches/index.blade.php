@extends('layout.master-layout')

@section('title', 'Ranches')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0">Ranches</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Ranches</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">

                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">All Ranches</h5>
                            <div class="flex-shrink-0">
                                <a href="{{ route('admin.ranches.create') }}" class="btn btn-primary">
                                    <i class="ri-add-line align-bottom me-1"></i> Add Ranch
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="ranchesTable"
                                class="table table-bordered dt-responsive nowrap table-striped align-middle"
                                style="width:100%">
                                <thead>
                                    <tr>
                                        <th>SR No.</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Acreage</th>
                                        <th>Marker</th>
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
        document.addEventListener('DOMContentLoaded', function() {

            const table = $('#ranchesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.ranches.datatable') }}',
                    type: 'GET'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        render: (d, t, r) => `<div class="d-flex align-items-center gap-2">
                    ${r.thumbnail
                        ? `<img src="/storage/${r.thumbnail}" class="rounded avatar-xs object-fit-cover" alt="">`
                        : `<div class="avatar-xs bg-light rounded d-flex align-items-center justify-content-center">
                                   <i class="ri-home-5-line text-muted fs-16"></i>
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
                        data: 'acreage_display',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'marker_preview',
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
                order: [
                    [7, 'desc']
                ],
                pageLength: 10,
                language: {
                    processing: '<div class="spinner-border text-primary spinner-border-sm" role="status"></div>'
                },
            });

            /* ── Delete ────────────────────────────────────────────────────────────── */
            $('#ranchesTable').on('click', '.delete-ranch', function() {
                const id = this.dataset.id;
                Alert.confirm('This ranch will be permanently removed.', {
                    title: 'Delete Ranch?',
                    type: 'danger',
                    confirmText: 'Yes, delete it',
                }).then(confirmed => {
                    if (!confirmed) return;
                    axios.delete(`/ranches/${id}`, {
                            data: {
                                _token: document.querySelector('meta[name="csrf-token"]')
                                    .content
                            }
                        })
                        .then(res => {
                            Toast.success(res.data.message);
                            table.ajax.reload(null, false);
                        })
                        .catch(err => Toast.fromResponse(err.response?.data));
                });
            });

            /* ── Toggle status ─────────────────────────────────────────────────────── */
            $('#ranchesTable').on('click', '.toggle-status', function() {
                const id = this.dataset.id;
                axios.patch(`/admin/ranches/${id}/toggle-status`, {
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
