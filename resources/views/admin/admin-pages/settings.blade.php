@extends('app-layouts.web-app-layout')

@section('content')
<div class="container mt-4">
    <div class="card shadow rounded-3">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Create New Device</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('devices.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Device Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Device Type</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="" disabled selected>Select device type</option>
                        <option value="irrigation">Irrigation</option>
                        <option value="sensor">Sensor</option>
                        <option value="controller">Controller</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" name="location" id="location" class="form-control" placeholder="E.g., Field A, Greenhouse 3" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Save Device</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
