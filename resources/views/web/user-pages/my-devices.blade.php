@extends('web.layouts.app-layout')

@section('page-title', 'My Devices | Smart Irrigation')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user/my-devices.css') }}">
@endpush

@section('content')
<div class="device-list-container">
    <div class="page-header">
        <h1 class="page-title">My Smart Devices</h1>
        <p class="page-subtitle">Monitor and control your irrigation system</p>
    </div>

    @if(!$devices->isEmpty())
        <div class="device-stats">
            <div class="stat-card">
                <div class="stat-number">{{ $devices->count() }}</div>
                <div class="stat-label">Total Devices</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $devices->where('status', 'online')->count() }}</div>
                <div class="stat-label">Online</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $devices->sum('total_valves') }}</div>
                <div class="stat-label">Total Valves</div>
            </div>
        </div>

        <div class="device-grid">
            @foreach ($devices as $device)
                <div class="device-card">
                    <div class="device-header">
                        <h3 class="device-number">{{ $device->device_number }}</h3>
                        <span class="device-status status-{{ strtolower($device->status) }}">
                            {{ ucfirst($device->status) }}
                        </span>
                    </div>

                    <div class="device-specs">
                        <div class="spec-item">
                            <div class="spec-value">{{ $device->total_valves }}</div>
                            <div class="spec-label">Valves</div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-value">{{ $device->total_flow_sensors }}</div>
                            <div class="spec-label">Flow</div>
                        </div>
                        <div class="spec-item">
                            <div class="spec-value">{{ $device->total_water_temp_sensors }}</div>
                            <div class="spec-label">Temp</div>
                        </div>
                    </div>

                    <div class="device-actions">
                        <a href="{{ route('device.control', ['device_number' => $device->device_number]) }}" 
                           class="btn btn-primary">
                            Control Device
                        </a>
                        <a href="#" class="btn btn-secondary">
                            View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-icon">🌱</div>
            <h3 class="empty-title">No Devices Found</h3>
            <p class="empty-text">
                You don't have any irrigation devices assigned to your account yet.<br>
                Contact your administrator or add a new device to get started.
            </p>
            <a href="#" class="btn-add-device">Add New Device</a>
        </div>
    @endif
</div>
@endsection