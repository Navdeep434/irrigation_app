@extends('admin.layouts.app-layout')

@section('page-title', 'Edit Customer | Smart Irrigation')

@section('content')
<div class="container mt-4">
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <a href="{{ url()->previous() }}" class="btn custom-header">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <hr>
    <h3 class="custom-header py-1 px-3 mb-4">Edit User</h3>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card glass-effect">
        <div class="card-body">
            <div id="success-message" class="alert alert-success d-none"></div>
            <div id="error-message" class="alert alert-danger d-none"></div>

            <form id="editCustomerForm" action="{{ route('admin.customer.update', $customer->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="customer_id" class="form-label">Customer ID</label>
                        <input type="text" name="customer_id" id="customer_id" class="form-control" value="{{ old('customer_id', $customer->uid) }}" readonly required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $customer->first_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $customer->last_name) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $customer->email) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="form-control" required>
                            <option value="male" {{ $customer->gender == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ $customer->gender == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ $customer->gender == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" name="dob" id="dob" class="form-control" value="{{ old('dob', $customer->dob) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="contact_number" class="form-label">Contact Number</label>
                        <div class="input-group">
                            <input type="text" name="country_code" class="form-control" value="{{ old('country_code', $customer->country_code ?? '+') }}" placeholder="+xx" style="max-width: 70px;" required>
                            <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $customer->contact_number) }}" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Customer</button>
            </form>
        </div>
    </div>

    <div class="card glass-effect mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Associated Devices</strong>
            <form id="attachDeviceForm" class="d-flex gap-2 align-items-center">
                @csrf
                <input type="hidden" name="user_id" value="{{ $customer->user_id }}">
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                <div class="input-group glass-input">
                    <select name="device_number" class="form-select" required style="width: 250px;">
                        <option value="">Select Device</option>
                        @foreach($unattachedDevices as $device)
                            <option value="{{ $device->device_number }}">{{ $device->device_number }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-link me-1"></i>
                </button>
            </form>
        </div>
        <div class="card-body">
            @if($customer->devices->isEmpty())
                <p>No devices associated with this customer.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Device Number</th>
                                <th>Status</th>
                                <th>Registered At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->devices as $device)
                            <tr id="device-row-{{ $device->id }}">
                                <td>{{ $device->device_number }}</td>
                                <td>
                                    @if($device->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $device->assigned_at ? \Carbon\Carbon::parse($device->assigned_at)->format('d M Y') : 'N/A' }}
                                </td>
                                <td>
                                    <button 
                                        class="btn btn-sm btn-danger unassociate-device-btn" 
                                        data-device-id="{{ $device->id }}">
                                        <i class="fas fa-unlink me-1"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#editCustomerForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const formData = $form.serialize();

        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    $('#success-message').text(response.message).removeClass('d-none');
                    $('#error-message').addClass('d-none');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                $('#error-message').text(message).removeClass('d-none');
                $('#success-message').addClass('d-none');
            }
        });
    });

    $('#attachDeviceForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.ajax({
            url: '{{ route("admin.customer.attachDevice") }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Failed to attach device.');
                }
            },
            error: function(xhr) {
                let message = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            }
        });
    });

    $('.unassociate-device-btn').on('click', function () {
        const deviceId = $(this).data('device-id');
        if (confirm('Are you sure you want to unassociate this device?')) {
            $.ajax({
                url: `/admin/devices/${deviceId}/unassociate`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#device-row-' + deviceId).remove();
                    } else {
                        alert('Failed to unassociate device.');
                    }
                },
                error: function() {
                    alert('An error occurred while unassociating the device.');
                }
            });
        }
    });
</script>
@endsection
