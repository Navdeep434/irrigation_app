@section('content')
<div class="container py-4">
    <h2 class="mb-4">Device Control Panel</h2>

    <!-- Temperature -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-thermometer-half me-2"></i>Temperature Sensor
        </div>
        <div class="card-body">
            <h4 class="card-title">Current Temperature: <span id="temperature" class="text-primary">--</span> °C</h4>
        </div>
    </div>

    <!-- Valve Controls -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
            <i class="fas fa-sliders-h me-2"></i>Valve Status
        </div>
        <div class="card-body">
            <div class="row" id="valve-status">
                <!-- Valve statuses will be injected here -->
            </div>
        </div>
    </div>

    <!-- Flow Sensors -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-info text-white">
            <i class="fas fa-tint me-2"></i>Flow Sensor Readings
        </div>
        <div class="card-body">
            <div class="row" id="flow-sensors">
                <!-- Flow sensor data will be injected here -->
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        const deviceNumber = @json($deviceNumber);

        function fetchDeviceData() {
            $.ajax({
                url: '/api/device/latest-data',
                method: 'GET',
                data: { device_id: deviceNumber },
                success: function(response) {
                    if (response.status === 'success') {
                        const data = response.data;

                        // Temperature
                        $('#temperature').text(data.temperature ?? '--');

                        // Valves
                        const valves = data.valves ? JSON.parse(data.valves) : {};
                        let valveHtml = '';
                        $.each(valves, function(key, status) {
                            valveHtml += `
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center ${status === 'ON' ? 'border-success' : 'border-secondary'}">
                                        <div class="card-body">
                                            <h5 class="card-title">Valve ${key}</h5>
                                            <span class="badge ${status === 'ON' ? 'bg-success' : 'bg-secondary'}">${status}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#valve-status').html(valveHtml);

                        // Flow Sensors
                        const flows = data.flow_sensors ? JSON.parse(data.flow_sensors) : {};
                        let flowHtml = '';
                        $.each(flows, function(key, rate) {
                            flowHtml += `
                                <div class="col-md-3 mb-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title">Sensor ${key}</h5>
                                            <p class="card-text text-info fw-bold">${rate} L/min</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        $('#flow-sensors').html(flowHtml);
                    } else {
                        console.error('Error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });
        }

        // Fetch every second
        setInterval(fetchDeviceData, 1000);
        fetchDeviceData();
    });
</script>
@endsection
