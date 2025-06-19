@extends('web.layouts.app-layout') 

@section('page-title', 'Dashboard | Smart Irrigation')

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
            <div class="label">Active Devices</div>
            <h2 class="value">5</h2>
            <div class="trend trend-up">
                <i class="fas fa-arrow-up"></i> 1 new device added
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
            <div class="icon" style="background-color: rgba(220, 53, 69, 0.1); color: #dc3545;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="label">Alerts</div>
            <h2 class="value">1</h2>
            <div class="trend">
                <i class="fas fa-info-circle"></i> Low Water Pressure
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

@endsection
