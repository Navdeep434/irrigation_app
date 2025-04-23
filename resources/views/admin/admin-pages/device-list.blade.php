@extends('admin.layouts.app-layout')

@section('page-title', 'Device List | Smart Irrigation')

@section('content')
<div class="container mt-4">
    <h2 class="custom-header py-1 px-3 mb-4">Device List</h2>
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
                <input type="text" class="form-control bg-transparent border-0 text-white shadow-none" id="searchInput" placeholder="Search by name or email...">
            </div>
        </div>
    </div>
    <hr>
    <div class="card glass-effect shadow rounded-3">
        <div class="card-body table-responsive">
            @if($devices->count())
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Device Number</th>
                            <th>User</th>
                            <th>Valves</th>
                            <th>Flow Sensors</th>
                            <th>Temperature Sensors</th>
                            <th>Status</th>
                            <th>Repair</th>
                            <th>Blocked</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $index => $device)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $device->device_number }}</td>
                                <td>{{ $device->user?->name ?? '—' }}</td>
                                <td>{{ $device->total_valves }}</td>
                                <td>{{ $device->total_flow_sensors }}</td>
                                <td>{{ $device->total_water_temp_sensors }}</td>
                                <td>
                                    <button 
                                        type="button"
                                        class="badge bg-{{ $device->status == 'active' ? 'success' : 'secondary' }} status-badge border-0"
                                        style="cursor: pointer;" 
                                        onclick="toggleDeviceStatus({{ $device->id }}, '{{ $device->status }}', '{{ $device->device_number }}')"
                                    >
                                        {{ ucfirst($device->status) }}
                                    </button>
                                </td>
                                <td>
                                    <button 
                                        type="button"
                                        class="badge bg-{{ $device->in_repair ? 'warning text-dark' : 'secondary' }} repair-badge border-0"
                                        style="cursor: pointer;" 
                                        onclick="toggleDeviceRepair({{ $device->id }}, {{ $device->in_repair ? 'true' : 'false' }}, '{{ $device->device_number }}')"
                                    >
                                        {{ $device->in_repair ? 'Yes' : 'No' }}
                                    </button>
                                </td>
                                <td>
                                    <button 
                                        type="button"
                                        class="badge bg-{{ $device->is_blocked ? 'danger' : 'secondary' }} blocked-badge border-0"
                                        style="cursor: pointer;" 
                                        onclick="toggleDeviceBlocked({{ $device->id }}, {{ $device->is_blocked ? 'true' : 'false' }}, '{{ $device->device_number }}')"
                                    >
                                        {{ $device->is_blocked ? 'Yes' : 'No' }}
                                    </button>
                                </td>
                                <td>{{ $device->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.devices.edit', $device->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $device->id }}, '{{ $device->device_number }}')">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted">No devices found.</p>
            @endif
        </div>
    </div>
</div>

<!-- Confirmation Modal for Toggling Device Status -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Toggle Device Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Do you want to <strong id="statusActionText"></strong> the device with number <strong id="modalDeviceNumber"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="toggleStatusBtn" class="btn btn-primary">Yes, proceed</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Toggling Device Repair Status -->
<div class="modal fade" id="repairModal" tabindex="-1" aria-labelledby="repairModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="repairModalLabel">Toggle Device Repair Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Do you want to <strong id="repairActionText"></strong> the device with number <strong id="repairDeviceNumber"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="toggleRepairBtn" class="btn btn-primary">Yes, proceed</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Toggling Device Blocked Status -->
<div class="modal fade" id="blockedModal" tabindex="-1" aria-labelledby="blockedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="blockedModalLabel">Toggle Device Blocked Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Do you want to <strong id="blockedActionText"></strong> the device with number <strong id="blockedDeviceNumber"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="toggleBlockedBtn" class="btn btn-primary">Yes, proceed</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal for Deleting Device -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the device with number <strong id="deleteDeviceNumber"></strong>?</p>
                <p class="text-danger">This action can be reversed from the trash section.</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Yes, delete</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global variables to store device information
    let selectedStatusDeviceId = null;
    let selectedRepairDeviceId = null;
    let selectedBlockedDeviceId = null;
    let selectedDeleteDeviceId = null;

    // Function to handle status toggle button click
    function toggleDeviceStatus(deviceId, status, deviceNumber) {
        // Store the device ID for later use
        selectedStatusDeviceId = deviceId;
        
        // Update modal text
        document.getElementById('modalDeviceNumber').textContent = deviceNumber;
        document.getElementById('statusActionText').textContent = status === 'active' ? 'deactivate' : 'activate';
        
        // Show the modal
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        statusModal.show();
    }
    
    // Function to handle repair toggle button click
    function toggleDeviceRepair(deviceId, inRepair, deviceNumber) {
        // Store the device ID for later use
        selectedRepairDeviceId = deviceId;
        
        // Update modal text
        document.getElementById('repairDeviceNumber').textContent = deviceNumber;
        document.getElementById('repairActionText').textContent = inRepair ? 'mark as not in repair' : 'mark as in repair';
        
        // Show the modal
        const repairModal = new bootstrap.Modal(document.getElementById('repairModal'));
        repairModal.show();
    }
    
    // Function to handle blocked toggle button click
    function toggleDeviceBlocked(deviceId, isBlocked, deviceNumber) {
        // Store the device ID for later use
        selectedBlockedDeviceId = deviceId;
        
        // Update modal text
        document.getElementById('blockedDeviceNumber').textContent = deviceNumber;
        document.getElementById('blockedActionText').textContent = isBlocked ? 'unblock' : 'block';
        
        // Show the modal
        const blockedModal = new bootstrap.Modal(document.getElementById('blockedModal'));
        blockedModal.show();
    }
    
    // Function to confirm delete action
    function confirmDelete(deviceId, deviceNumber) {
        // Store the device ID for later use
        selectedDeleteDeviceId = deviceId;
        
        // Update modal text
        document.getElementById('deleteDeviceNumber').textContent = deviceNumber;
        
        // Show the modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // When document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listener for the status toggle button in the modal
        document.getElementById('toggleStatusBtn').addEventListener('click', function() {
            if (!selectedStatusDeviceId) return;
            
            // Create AJAX request
            fetch(`/admin/devices/${selectedStatusDeviceId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reload the page to show the updated status
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            });
            
            // Hide the modal
            const statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
            statusModal.hide();
        });
        
        // Add event listener for the repair toggle button in the modal
        document.getElementById('toggleRepairBtn').addEventListener('click', function() {
            if (!selectedRepairDeviceId) return;
            
            // Create AJAX request
            fetch(`/admin/devices/${selectedRepairDeviceId}/toggle-repair`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reload the page to show the updated status
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            });
            
            // Hide the modal
            const repairModal = bootstrap.Modal.getInstance(document.getElementById('repairModal'));
            repairModal.hide();
        });
        
        // Add event listener for the blocked toggle button in the modal
        document.getElementById('toggleBlockedBtn').addEventListener('click', function() {
            if (!selectedBlockedDeviceId) return;
            
            // Create AJAX request
            fetch(`/admin/devices/${selectedBlockedDeviceId}/toggle-blocked`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reload the page to show the updated status
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            });
            
            // Hide the modal
            const blockedModal = bootstrap.Modal.getInstance(document.getElementById('blockedModal'));
            blockedModal.hide();
        });
        
        // Add event listener for the delete button in the modal
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!selectedDeleteDeviceId) return;
            
            // Create AJAX request for soft delete
            fetch(`/admin/devices/${selectedDeleteDeviceId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reload the page to show the updated list
                window.location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong. Please try again.');
            });
            
            // Hide the modal
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            deleteModal.hide();
        });
        
        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.indexOf(searchValue) > -1 ? '' : 'none';
            });
        });
    });
</script>

@endsection