@extends('admin.layouts.app-layout')

@section('page-title', 'Edit Device | Smart Irrigation')

@section('content')
<div class="container mt-4">
    <h2 class="custom-header py-1 px-3 mb-4">Edit Device</h2>
    <hr>
    <div class="card glass-effect shadow rounded-3">
        <div class="card-body">
            <form action="{{ route('admin.devices.update', $device->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="device_number" class="form-label">Device Number</label>
                        <input type="text" name="device_number" class="form-control" id="device_number" value="{{ $device->device_number }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">User</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">Select a user</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $user->id == $device->user_id ? 'selected' : '' }}>
                                {{ $user->first_name . ' ' . $user->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="total_valves" class="form-label">Valves</label>
                        <input type="number" name="total_valves" class="form-control" id="total_valves" value="{{ $device->total_valves }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="total_flow_sensors" class="form-label">Flow Sensors</label>
                        <input type="number" name="total_flow_sensors" class="form-control" id="total_flow_sensors" value="{{ $device->total_flow_sensors }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="total_water_temp_sensors" class="form-label">Temperature Sensors</label>
                        <input type="number" name="total_water_temp_sensors" class="form-control" id="total_water_temp_sensors" value="{{ $device->total_water_temp_sensors }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="active" {{ $device->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $device->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="in_repair" class="form-label">Repair</label>
                        <select name="in_repair" id="in_repair" class="form-select">
                            <option value="0" {{ !$device->in_repair ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $device->in_repair ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="is_blocked" class="form-label">Blocked</label>
                        <select name="is_blocked" id="is_blocked" class="form-select">
                            <option value="0" {{ !$device->is_blocked ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $device->is_blocked ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.devices.list') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Device</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection