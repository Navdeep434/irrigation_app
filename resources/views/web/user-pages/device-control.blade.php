@extends('web.layouts.app-layout')

@section('page-title', 'Valve Control | Smart Irrigation')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user/device-control.css') }}">
@endpush

@section('content')
<div class="valve-dashboard">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-valve"></i>
            Valve Control Center
        </h1>
        <p class="page-subtitle">Monitor and control irrigation valves for optimal water management</p>
    </div>

    <!-- Control Panel -->
    <div class="control-panel">
        <div class="control-stats">
            <div class="stat-item">
                <div class="stat-value">{{ $selectedDevice->total_valves }}</div>
                <div class="stat-label">Total Valves</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="active-valves">0</div>
                <div class="stat-label">Active Valves</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $selectedDevice->total_flow_sensors }}</div>
                <div class="stat-label">Flow Sensors</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">92%</div>
                <div class="stat-label">System Efficiency</div>
            </div>
        </div>
    </div>

    <!-- Control Header -->
    <div class="valve-dashboard-header">
        <div class="group-toggle">
            <label class="switch">
                <input type="checkbox" id="toggle-all">
                <span class="slider round"></span>
            </label>
            <span>Master Control</span>
        </div>
        <div class="temperature">
            <i class="fas fa-thermometer-half"></i>
            <span>Temperature: <strong>-- °C</strong></span>
        </div>
    </div>

    <!-- Device Selector -->
    <div class="device-selector">
        <form method="GET" action="{{ route('device.control') }}">
            <label for="device_number">
                <i class="fas fa-microchip"></i> Select Device:
            </label>
            <select name="device_number" id="device_number" onchange="this.form.submit()">
                @foreach ($devices as $device)
                    <option value="{{ $device->device_number }}" {{ request('device_number') == $device->device_number ? 'selected' : '' }}>
                        {{ $device->device_number }} ({{ $device->total_valves }} Valves, {{ $device->total_flow_sensors }} Sensors)
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Valve Grid -->
    <div class="valve-grid">
        @foreach (range(1, $selectedDevice->total_valves) as $valve)
            <div class="valve-box" id="valve-{{ $valve }}">
                <div class="valve-header">
                    Valve {{ $valve }}
                </div>

                <div class="valve-status">
                    <label class="switch">
                        <input type="checkbox" class="valve-toggle" data-valve="{{ $valve }}" data-device="{{ $selectedDevice->device_number }}">
                        <span class="slider round"></span>
                    </label>
                </div>

                <div class="status-indicator">
                    <span class="status-dot" id="status-dot-{{ $valve }}"></span>
                    <span id="status-text-{{ $valve }}">Inactive</span>
                </div>

                <div class="flow-rate">
                    <i class="fas fa-tint"></i>
                    Flow Rate: <strong id="flow-{{ $valve }}">-- L/min</strong>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        const uid = @json($uid);
        const token = $('meta[name="csrf-token"]').attr('content');
        const deviceNumber = @json($selectedDevice->device_number);

        function updateActiveCount() {
            const activeCount = $('.valve-toggle:checked').length;
            $('#active-valves').text(activeCount);
        }

        function updateValveStatus(valve, isActive) {
            const valveBox = $('#valve-' + valve);
            const statusDot = $('#status-dot-' + valve);
            const statusText = $('#status-text-' + valve);

            if (isActive) {
                valveBox.addClass('active');
                statusDot.addClass('active');
                statusText.text('Active');
            } else {
                valveBox.removeClass('active');
                statusDot.removeClass('active');
                statusText.text('Inactive');
            }
            updateActiveCount();
        }

        $('.valve-toggle').on('change', function () {
            const valve = $(this).data('valve');
            const action = $(this).is(':checked') ? 'on' : 'off';
            const valveBox = $('#valve-' + valve);

            valveBox.addClass('loading');

            $.ajax({
                url: "{{ route('valve.send') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token
                },
                data: {
                    uid: uid,
                    device_number: deviceNumber,
                    valve_number: valve,
                    action: action
                },
                success: function () {
                    updateValveStatus(valve, action === 'on');
                    valveBox.removeClass('loading');
                    showNotification('Valve ' + valve + ' ' + action.toUpperCase(), 'success');
                },
                error: function () {
                    valveBox.removeClass('loading');
                    $(this).prop('checked', !$(this).is(':checked'));
                    showNotification('Failed to toggle valve ' + valve, 'error');
                }
            });
        });

        $('#toggle-all').on('change', function () {
            const action = $(this).is(':checked') ? 'on' : 'off';
            const masterToggle = $(this);
            masterToggle.prop('disabled', true);

            $('.valve-toggle').each(function () {
                const valve = $(this).data('valve');
                const valveBox = $('#valve-' + valve);
                $(this).prop('checked', action === 'on');
                valveBox.addClass('loading');

                $.ajax({
                    url: "{{ route('valve.send') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    data: {
                        uid: uid,
                        device_number: deviceNumber,
                        valve_number: valve,
                        action: action
                    },
                    success: function () {
                        updateValveStatus(valve, action === 'on');
                        valveBox.removeClass('loading');
                    },
                    error: function () {
                        valveBox.removeClass('loading');
                    },
                    complete: function () {
                        if ($('.valve-box.loading').length === 0) {
                            masterToggle.prop('disabled', false);
                        }
                    }
                });
            });

            showNotification('All valves turned ' + action.toUpperCase(), 'info');
        });

        function showNotification(message, type) {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }

        function fetchLiveReadings() {
            $.ajax({
                url: "/device/" + deviceNumber + "/latest-reading",
                method: "GET",
                success: function (data) {
                    console.log('Received data:', data); 
                    
                    if (!data) return;

                    // Check if temperature exists
                    if (data.temperature) {
                        $('.temperature strong').text(data.temperature + '°C');
                    } else {
                        console.log('Temperature data missing');
                    }

                    // Check flow rates
                    for (let i = 1; i <= {{ $selectedDevice->total_valves }}; i++) {
                        const flowKey = 'flow_rate' + i;
                        const flow = data[flowKey] ?? 0;
                        console.log(`Flow rate ${i}:`, flow); // Add this line
                        $('#flow-' + i).text(flow.toFixed(1) + ' L/min');
                    }
                },
                error: function (xhr) {
                    console.error("Failed to fetch live data", xhr);
                    console.log('Response:', xhr.responseText); // Add this line
                }
            });
        }

        setInterval(fetchLiveReadings, 2000);


        updateActiveCount();
    });
</script>
@endsection
