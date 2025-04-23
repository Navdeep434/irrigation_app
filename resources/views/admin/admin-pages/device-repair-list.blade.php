@extends('admin.layouts.app-layout')
@section('page-title', 'Devices in Repair | Smart Irrigation')
@section('content')
<div class="container mt-4">
    <h2 class="custom-header py-1 px-3 mb-4">Devices in Repair</h2>
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

    @if($devices->isEmpty())
        <div class="alert alert-info">No devices currently in repair.</div>
    @else
    <table class="table table-bordered table-striped" id="repairTable">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Device Number</th>
                <th>User</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($devices as $device)
            <tr data-id="{{ $device->id }}">
                <td>{{ $loop->iteration }}</td>
                <td class="device-number">{{ $device->device_number }}</td>
                <td>{{ optional($device->user)->name ?? '-' }}</td>
                <td>{{ optional($device->customer)->name ?? '-' }}</td>
                <td class="status-text">{{ ucfirst($device->status) }}</td>
                <td>{{ $device->updated_at->format('Y-m-d H:i') }}</td>
                <td>
                    <button
                        class="btn btn-sm toggle-repair-btn"
                        data-in-repair="{{ $device->in_repair ? '1' : '0' }}"
                        data-device-id="{{ $device->id }}"
                    >
                        {{ $device->in_repair ? 'Mark Repaired' : 'Mark Repairing' }}
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark text-white">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Status Change</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="confirmText"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Cancel
        </button>
        <button type="button" class="btn btn-primary" id="confirmToggle">
          Yes, proceed
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    let targetRow, targetBtn;

    // Open modal when toggle button clicked
    $('.toggle-repair-btn').on('click', function() {
        targetBtn = $(this);
        targetRow = targetBtn.closest('tr');
        const inRepair = targetBtn.data('in-repair') === '1';
        const deviceNum = targetRow.find('.device-number').text();
        $('#confirmText').text(
            `Are you sure you want to mark device ${deviceNum} as ` +
            (inRepair ? 'repaired?' : 'in repair?')
        );
        $('#confirmModal').modal('show');
    });

    // On confirm, send AJAX and update UI
    $('#confirmToggle').on('click', function() {
        const deviceId = targetRow.data('id');
        $.ajax({
            url: `/admin/devices/${deviceId}/toggle-repair`,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(data) {
                // Update the status button and row text
                const inRepair = targetBtn.data('in-repair') === '1';
                targetBtn.data('in-repair', inRepair ? '0' : '1');
                targetBtn.text(inRepair ? 'Mark Repairing' : 'Mark Repaired');
                targetRow.find('.status-text').text(inRepair ? 'Active' : 'Repairing');

                // Show confirmation message as a toast/alert
                const alertBox = $('<div class="alert alert-success position-fixed top-0 end-0 m-3"></div>')
                    .text(data.message)
                    .appendTo('body');
                setTimeout(function() {
                    alertBox.remove();
                }, 3000);

                // Hide the modal
                $('#confirmModal').modal('hide');
            },
            error: function(err) {
                console.error('Error:', err);
                alert('An error occurred while updating the device status.');
            }
        });
    });
});
</script>
@endsection