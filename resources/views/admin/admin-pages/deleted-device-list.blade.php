@extends('admin.layouts.app-layout')

@section('page-title', 'Trashed Devices | Smart Irrigation')

@section('content')
<div class="container mt-4">
    <h2 class="custom-header py-1 px-3 mb-4">Trashed Devices</h2>
    <hr>
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <a href="{{ url()->previous() }}" class="btn custom-header">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="col-md-4 ps-0">
            <div class="input-group glass-input">
                <span class="input-group-text bg-transparent border-0" id="search-addon"><i class="fa fa-search"></i></span>
                <input type="text" class="form-control bg-transparent border-0 text-white shadow-none" id="searchInput" placeholder="Search by device number...">
            </div>
        </div>
    </div>
    <hr>

    <div class="card glass-effect shadow rounded-3">
        <div class="card-body table-responsive">
            @if($trashedDevices->count())
                <table class="table table-bordered table-striped align-middle" id="deviceTable">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Device Number</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trashedDevices as $index => $device)
                            <tr id="device-row-{{ $device->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $device->device_number }}</td>
                                <td>{{ $device->user?->name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $device->status == 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($device->status) }}
                                    </span>
                                </td>
                                <td>{{ $device->deleted_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success restore-btn" data-id="{{ $device->id }}" data-number="{{ $device->device_number }}">
                                        <i class="fa fa-trash-restore"></i> Restore
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">No trashed devices found.</p>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Yes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let actionType = '';
    let selectedId = '';

    $(document).ready(function () {
        // Search functionality
        $('#searchInput').on('input', function () {
            const value = $(this).val().toLowerCase();
            $('#deviceTable tbody tr').filter(function () {
                $(this).toggle($(this).children('td').eq(1).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Restore button click
        $('.restore-btn').on('click', function () {
            selectedId = $(this).data('id');
            const deviceNumber = $(this).data('number');
            actionType = 'restore';

            $('#confirmModalTitle').text('Restore Device');
            $('#confirmModalBody').html(`Are you sure you want to restore device <strong>${deviceNumber}</strong>?`);
            $('#confirmModal').modal('show');
        });

        // Confirm action button click
        $('#confirmActionBtn').on('click', function () {
            if (!selectedId || !actionType) return;

            let url = '';
            let type = 'POST';
            let data = { _token: '{{ csrf_token() }}' };

            if (actionType === 'restore') {
                url = `{{ route('admin.devices.restore', ':id') }}`.replace(':id', selectedId);
            }

            $.ajax({
                url: url,
                type: type,
                data: data,
                success: function (response) {
                    $('#confirmModal').modal('hide');
                    
                    if ($('#deviceTable tbody tr').length === 1) {
                        // If this was the last row, refresh the page
                        location.reload();
                    } else {
                        // Remove the row from the table
                        $(`#device-row-${selectedId}`).fadeOut('slow', function() {
                            $(this).remove();
                            // Re-index the remaining rows
                            $('#deviceTable tbody tr').each(function(index) {
                                $(this).find('td:first').text(index + 1);
                            });
                        });
                        
                        // Show success message
                        alert(response.message || 'Action completed successfully.');
                    }
                },
                error: function (xhr) {
                    $('#confirmModal').modal('hide');
                    alert(xhr.responseJSON?.message || 'Something went wrong. Please try again.');
                }
            });
        });
    });
</script>
@endsection