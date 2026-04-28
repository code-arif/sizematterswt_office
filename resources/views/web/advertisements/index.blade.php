{{-- resources/views/web/advertisements/index.blade.php --}}
@extends('layout.master-layout')
@section('title', 'Advertisements')

@section('content')
    <div class="page-content">
        <div class="container-fluid">

            {{-- Page Header --}}
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Advertisements</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="{{ route('show.admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Advertisements</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0 flex-grow-1">All Advertisements</h5>
                            <a href="{{ route('admin.advertisements.create') }}" class="btn btn-success btn-sm">
                                <i class="ri-add-line me-1"></i> Add Advertisement
                            </a>
                        </div>

                        <div class="card-body">
                            {{-- FIX 1: wrap in table-responsive so the table can scroll horizontally
                            instead of breaking out of the card --}}
                            <div class="table-responsive">
                                <table id="adsTable" class="table table-bordered table-nowrap align-middle w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">#</th>
                                            <th width="80">Banner</th>
                                            <th width="110">Advertiser</th>
                                            <th>Title</th>
                                            <th width="160">Linked To</th>
                                            <th width="80">Radius</th>
                                            <th width="160">Schedule</th>
                                            <th width="100" class="text-center">Impressions</th>
                                            <th width="90">Status</th>
                                            <th width="110">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Delete Confirm Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="ri-delete-bin-line text-danger fs-48 d-block mb-3"></i>
                    <h5>Are you sure?</h5>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmDelete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            var table = $('#adsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route("admin.advertisements.datatable") }}',
                // FIX 2: enable horizontal scroll inside DataTables itself
                scrollX: true,
                autoWidth: false,
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '40px' },
                    {
                        data: 'image', name: 'image', orderable: false, searchable: false, width: '80px',
                        render: function (data, type, row) {
                            if (row.media_type === 'video') {
                                return '<div class="position-relative">' +
                                    '<video src="/storage/' + data + '" class="rounded" style="width:70px;height:42px;object-fit:cover;"></video>' +
                                    '<div class="position-absolute top-50 start-50 translate-middle">' +
                                    '<i class="ri-play-circle-fill text-white fs-20"></i>' +
                                    '</div></div>';
                            }
                            return '<img src="/storage/' + data + '" class="rounded" style="width:70px;height:42px;object-fit:cover;" />';
                        }
                    },
                    { data: 'advertiser', name: 'advertiser', orderable: false, width: '110px' },
                    {
                        data: 'title',
                        name: 'title',
                        width: '220px',
                        // FIX 3: truncate long title/subtitle so it never blows out the column
                        render: function (data, type, row) {
                            var title = $('<div>').text(data).html();           // XSS-safe
                            var sub = '';
                            if (row.subtitle) {
                                var truncated = row.subtitle.length > 60
                                    ? row.subtitle.substring(0, 60) + '…'
                                    : row.subtitle;
                                sub = '<small class="text-muted d-block text-truncate" style="max-width:200px;" title="'
                                    + $('<div>').text(row.subtitle).html() + '">'
                                    + $('<div>').text(truncated).html()
                                    + '</small>';
                            }
                            return '<strong>' + title + '</strong>' + sub;
                        }
                    },
                    { data: 'linked_to', name: 'linked_to', orderable: false, width: '160px' },
                    { data: 'radius_label', name: 'radius_label', orderable: false, searchable: false, width: '80px' },
                    { data: 'schedule', name: 'schedule', orderable: false, searchable: false, width: '160px' },
                    { data: 'impression_count', name: 'impression_count', className: 'text-center', width: '100px' },
                    { data: 'status_badge', name: 'status', orderable: false, width: '90px' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, width: '110px' },
                ],
                order: [[0, 'desc']],
                language: { processing: '<span class="spinner-border spinner-border-sm"></span> Loading...' },
            });

            // ── Toggle status ──────────────────────────────────────────────────────
            $(document).on('click', '.toggle-ad-status', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: '/admin/advertisements/' + id + '/toggle-status',
                    method: 'PATCH',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        table.ajax.reload(null, false);
                        toastr.success(res.message);
                    }
                });
            });

            // ── Delete ─────────────────────────────────────────────────────────────
            var deleteId = null;
            $(document).on('click', '.delete-ad', function () {
                deleteId = $(this).data('id');
                new bootstrap.Modal('#deleteModal').show();
            });

            $('#confirmDelete').on('click', function () {
                if (!deleteId) return;
                $.ajax({
                    url: '/admin/advertisements/' + deleteId,
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                    success: function (res) {
                        bootstrap.Modal.getInstance('#deleteModal').hide();
                        table.ajax.reload(null, false);
                        toastr.success(res.message);
                        deleteId = null;
                    },
                    error: function () { toastr.error('Delete failed.'); }
                });
            });
        });
    </script>
@endpush
