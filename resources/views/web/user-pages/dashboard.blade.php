@extends('web.layouts.app-layout') 

@section('page-title', 'Dashboard | Smart Irrigation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/dashboard.css') }}" />
@endpush

@section('content')
<!-- Dashboard Content -->
<div class="dashboard-content">
    <!-- Summary Cards -->
    <div class="dashboard-summary">
        <div class="summary-card">
            <div class="icon" style="background-color: rgba(44, 142, 113, 0.1); color: var(--primary-color);">
                <i class="fas fa-tint"></i>
            </div>
            <div class="label">Water Usage Today</div>
            <h2 class="value">28.5 L</h2>
            <div class="trend trend-down">
                <i class="fas fa-arrow-down"></i> 12% from yesterday
            </div>
        </div>
        
        <div class="summary-card">
            <div class="icon" style="background-color: rgba(255, 193, 7, 0.1); color: var(--accent-color);">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="label">Total Devices</div>
            <h2 class="value">{{ $devices->count() }}</h2>
            <div class="trend trend-up">
                <i class="fas fa-plus-circle"></i> {{ $devices->where('status', 'online')->count() }} online
            </div>
        </div>
        
        <div class="summary-card">
            <div class="icon" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                <i class="fas fa-valve"></i>
            </div>
            <div class="label">Total Valves</div>
            <h2 class="value">{{ $devices->sum('total_valves') }}</h2>
            <div class="trend">
                <i class="fas fa-cog"></i> Across all devices
            </div>
        </div>
        
        <div class="summary-card">
            <div class="icon" style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="label">Alerts</div>
            <h2 class="value">{{ $devices->where('status', 'offline')->count() }}</h2>
            <div class="trend">
                <i class="fas fa-info-circle"></i> Offline devices
            </div>
        </div>
    </div>

    <!-- Device Status Summary -->
    <div class="status-summary">
        <h3><i class="fas fa-devices"></i> Device Status Overview</h3>
        <div class="status-grid">
            @forelse($devices as $device)
                <div class="device-status">
                    <div class="device-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="device-name">{{ $device->device_number }}</div>
                    <div class="status-text">
                        <span class="status-indicator 
                            @if($device->status == 'online') status-active
                            @elseif($device->status == 'offline') status-inactive
                            @elseif($device->status == 'maintenance') status-warning
                            @else status-error
                            @endif"></span>
                        {{ ucfirst($device->status) }}
                    </div>
                    <div class="device-specs" style="margin-top: 8px; font-size: 0.8rem; color: #6c757d;">
                        {{ $device->total_valves }}V | {{ $device->total_flow_sensors }}F | {{ $device->total_water_temp_sensors }}T
                    </div>
                </div>
            @empty
                <div class="device-status" style="grid-column: 1 / -1; text-align: center; padding: 30px;">
                    <div class="device-icon" style="color: #6c757d;">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="device-name">No Devices</div>
                    <div class="status-text" style="color: #6c757d;">
                        Add your first device to get started
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Additional Statistics Cards -->
    <div class="dashboard-summary">
        <div class="summary-card">
            <div class="icon" style="background-color: rgba(40, 167, 69, 0.1); color: #28a745;">
                <i class="fas fa-stream"></i>
            </div>
            <div class="label">Flow Sensors</div>
            <h2 class="value">{{ $devices->sum('total_flow_sensors') }}</h2>
            <div class="trend">
                <i class="fas fa-water"></i> Monitoring water flow
            </div>
        </div>
        
        <div class="summary-card">
            <div class="icon" style="background-color: rgba(255, 99, 132, 0.1); color: #ff6384;">
                <i class="fas fa-thermometer-half"></i>
            </div>
            <div class="label">Temperature Sensors</div>
            <h2 class="value">{{ $devices->sum('total_water_temp_sensors') }}</h2>
            <div class="trend">
                <i class="fas fa-temperature-low"></i> Water temperature
            </div>
        </div>
        
        <div class="summary-card">
            <div class="icon" style="background-color: rgba(13, 110, 253, 0.1); color: #0d6efd;">
                <i class="fas fa-clock"></i>
            </div>
            <div class="label">Next Scheduled Watering</div>
            <h2 class="value">2h 15m</h2>
            <div class="trend">
                <i class="fas fa-calendar-check"></i> Zone 2 - Garden
            </div>
        </div>
        
        <div class="summary-card">
            <div class="icon" style="background-color: rgba(106, 90, 205, 0.1); color: #6a5acd;">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="label">System Efficiency</div>
            <h2 class="value">94%</h2>
            <div class="trend trend-up">
                <i class="fas fa-arrow-up"></i> 3% improvement
            </div>
        </div>
    </div>
    
    <!-- Weather Forecast -->
    <div class="weather-section">
        <h3><i class="fas fa-cloud-sun"></i> Current Weather</h3>
        @if (!empty($currentWeather))
            <div class="forecast-item text-center p-3 border rounded shadow-sm">
                <div class="day fw-bold mb-2">
                    {{ \Carbon\Carbon::now()->format('l') }}
                </div>
                <div class="weather-icon mb-2" style="font-size: 24px;">
                    @php
                        $weatherIcon = $currentWeather['weather'][0]['main'] ?? 'Clouds';
                        $iconMap = [
                            'Clear' => 'fa-sun',
                            'Clouds' => 'fa-cloud',
                            'Rain' => 'fa-cloud-showers-heavy',
                            'Drizzle' => 'fa-cloud-rain',
                            'Thunderstorm' => 'fa-bolt',
                            'Snow' => 'fa-snowflake',
                            'Mist' => 'fa-smog',
                            'Fog' => 'fa-smog',
                            'Haze' => 'fa-smog',
                        ];
                    @endphp
                    <i class="fas {{ $iconMap[$weatherIcon] ?? 'fa-cloud' }}"></i>
                </div>
                <div class="temp fs-5">{{ round($currentWeather['main']['temp']) }}°C</div>
                <div class="desc text-muted">{{ $currentWeather['weather'][0]['description'] }}</div>
            </div>
        @else
            <p class="text-muted">Weather data not available.</p>
        @endif
    </div>

    <!-- Recent Activities -->
    <div class="activities-container">
        <h3><i class="fas fa-history"></i> Recent Activities</h3>
        <div class="activities-list">
            @if($devices->count() > 0)
                @foreach($devices->take(5) as $device)
                    <div class="activity-item">
                        <div class="activity-icon bg-activity-system">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="activity-details">
                            <p class="activity-message">Device {{ $device->device_number }} is {{ $device->status }}</p>
                            <p class="activity-time">{{ \Carbon\Carbon::now()->subMinutes(rand(5, 60))->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
                
                <div class="activity-item">
                    <div class="activity-icon bg-activity-water">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="activity-details">
                        <p class="activity-message">Irrigation cycle completed for Zone 1</p>
                        <p class="activity-time">2 hours ago</p>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon bg-activity-schedule">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="activity-details">
                        <p class="activity-message">Next watering scheduled for Zone 2</p>
                        <p class="activity-time">3 hours ago</p>
                    </div>
                </div>
            @else
                <div class="activity-item" style="justify-content: center; text-align: center; padding: 40px 0;">
                    <div class="activity-details">
                        <p class="activity-message" style="color: #6c757d;">No recent activities</p>
                        <p class="activity-time">Add devices to see activity logs</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection