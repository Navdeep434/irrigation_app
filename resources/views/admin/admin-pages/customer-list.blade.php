@extends('admin.layouts.app-layout')

@section('page-title', 'Customer List | Smart Irrigation')

@section('content')
<div class="container mt-4">
    <h3 class="custom-header py-1 px-3 mb-4">Customer List</h3>
    <hr>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row mb-3">
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
    <div class="table-responsive">
        <table class="table table-bordered glass-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Customer ID</th>
                    <th>Device ID</th>
                    <th>Blocked</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $index => $customer)
                <tr data-customer-id="{{ $customer->id }}" data-name="{{ strtolower($customer->first_name . ' ' . $customer->last_name) }}" data-email="{{ strtolower($customer->email) }} " data-uid="{{ strtolower($customer->uid) }}" data-device="{{ strtolower($customer->device->device_number ?? '' )}}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->uid }}</td>
                    <td>
                        @if($customer->devices->isNotEmpty())
                            @foreach($customer->devices as $device)
                                <span class="badge bg-primary">{{ $device->device_number }}</span><br>
                            @endforeach
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm {{ $customer->is_blocked ? 'btn-warning' : 'btn-secondary' }} trigger-block-modal"
                            data-id="{{ $customer->id }}" data-blocked="{{ $customer->is_blocked ? '1' : '0' }}">
                            {{ $customer->is_blocked ? 'Blocked' : 'Unblocked' }}
                        </button>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input trigger-status-modal" type="checkbox" role="switch"
                                id="statusSwitch{{ $customer->id }}" data-id="{{ $customer->id }}"
                                {{ $customer->status ? 'checked' : '' }}>
                        </div>
                    </td>
                    <td>
                        <!-- Changed from anchor to button -->
                        <button type="button" class="btn btn-sm btn-primary edit-customer-btn" 
                            onclick="window.location.href='{{ route('admin.customer.edit', $customer->id) }}'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-customer-btn" 
                            data-id="{{ $customer->id }}" data-bs-toggle="modal"
                            data-bs-target="#deleteCustomerModal{{ $customer->id }}">
                            <i class="fas fa-trash"></i> 
                        </button>
                    </td>
                </tr>

                <!-- Delete Modal -->
                <div class="modal fade" id="deleteCustomerModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form class="delete-customer-form" data-id="{{ $customer->id }}"
                              action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Delete Customer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this customer?
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">Yes, delete</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $customers->links() }}
</div>

<!-- Block/Unblock Confirmation Modal -->
<div class="modal fade" id="blockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Block Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="blockModalBody">Are you sure?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmBlockBtn">Yes, Confirm</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Toggle Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Customer Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="statusModalBody">Do you want to change the active status?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="confirmStatusBtn">Yes, Confirm</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    let selectedCustomerId = null;
    let selectedButton = null;
    let selectedCheckbox = null;

    $(document).ready(function () {
        // Block Modal trigger
        $('.trigger-block-modal').click(function () {
            selectedCustomerId = $(this).data('id');
            selectedButton = $(this);
            const isBlocked = $(this).data('blocked') === '1';
            $('#blockModalBody').text(isBlocked ? 'Unblock this customer?' : 'Block this customer?');
            $('#blockModal').modal('show');
        });

        $('#confirmBlockBtn').click(function () {
            const isCurrentlyBlocked = selectedButton.data('blocked') === '1';
            const action = isCurrentlyBlocked ? 'unblock' : 'block';

            $.ajax({
                url: `/admin/customers/${selectedCustomerId}/toggleBlock`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({
                    action: action
                }),
                success: function (response) {
                    if (response.success) {
                        const newState = !isCurrentlyBlocked;
                        selectedButton.text(newState ? 'Blocked' : 'Unblocked');
                        selectedButton.data('blocked', newState ? '1' : '0');
                        selectedButton.removeClass(isCurrentlyBlocked ? 'btn-warning' : 'btn-secondary')
                                     .addClass(isCurrentlyBlocked ? 'btn-secondary' : 'btn-warning');
                        $('#blockModal').modal('hide');
                    } else {
                        alert('Failed to update block status: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'An error occurred while updating block status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        });

        // Status Modal trigger
        $('.trigger-status-modal').change(function(e) {
            e.preventDefault();
            const originalState = $(this).prop('checked');
            $(this).prop('checked', !originalState);
            
            selectedCustomerId = $(this).data('id');
            selectedCheckbox = $(this);
            $('#statusModalBody').text(originalState ? 'Activate this customer?' : 'Deactivate this customer?');
            $('#statusModal').modal('show');
        });

        $('#confirmStatusBtn').click(function () {
            $.ajax({
                url: `/admin/customers/${selectedCustomerId}/toggle-status`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify({}),
                success: function (response) {
                    if (response.success) {
                        // Toggle the checkbox
                        const newState = !selectedCheckbox.prop('checked');
                        selectedCheckbox.prop('checked', newState);
                        $('#statusModal').modal('hide');
                    } else {
                        alert('Failed to update status: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'An error occurred while updating status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        });

        // AJAX delete
        $('.delete-customer-form').submit(function (e) {
            e.preventDefault();
            const form = $(this);
            const id = form.data('id');
            const modal = form.closest('.modal');

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    if (response.success) {
                        $(`tr[data-customer-id="${id}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                        modal.modal('hide');
                    } else {
                        alert('Failed to delete customer: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function (xhr) {
                    let errorMessage = 'Error occurred during deletion.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                }
            });
        });

        // Search
        $('#searchInput').on('keyup', function () {
            const value = $(this).val().toLowerCase();
            if (value === '') {
                $('table tbody tr').show();
                return;
            }
            
            $('table tbody tr').each(function () {
                const name = $(this).data('name');
                const email = $(this).data('email');
                const uid = $(this).data('uid');
                const device = $(this).data('device');
                
                if (name.includes(value) || 
                    email.includes(value) || 
                    uid.includes(value) || 
                    device.includes(value)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>
@endsection