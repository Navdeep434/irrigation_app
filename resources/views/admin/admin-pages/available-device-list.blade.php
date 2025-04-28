@extends('admin.layouts.app-layout')
@section('page-title', 'Available Devices | Smart Irrigation')
@section('content')
<div class="container mt-4">
    <h2 class="custom-header py-1 px-3 mb-4">Available Devices</h2>
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
                <input type="text" class="form-control bg-transparent border-0 text-white shadow-none" id="searchInput" placeholder="Search..." onkeyup="searchTable()">
            </div>
        </div>

        <div class="col-auto ms-auto">
            <a href="{{ route('admin.devices.create') }}" class="btn btn-primary">
                <i class="fa fa-plus-circle"></i> Add Device
            </a>
        </div>
    </div>
    <hr>
    <div class="card glass-effect shadow rounded-3">
        @if($devices->isEmpty())
            <p class="text-center">No unassociated devices found.</p>
        @else
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle" id="devicesTable">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Device Number</th>
                            <th>Valves</th>
                            <th>Flow Sensors</th>
                            <th>Temperature Sensors</th>
                            <th>Status</th>
                            <th>Added On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $index => $device)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $device->device_number }}</td>
                                <td>{{ $device->total_valves }}</td>
                                <td>{{ $device->total_flow_sensors }}</td>
                                <td>{{ $device->total_water_temp_sensors }}</td>
                                <td>
                                    @if($device->status === 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge bg-success">{{ ucfirst($device->status) }}</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($device->created_at)->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
    function searchTable() {
        var input, filter, table, tr, td, i, j, txtValue;
        input = document.getElementById('searchInput');
        filter = input.value.toUpperCase();
        table = document.getElementById('devicesTable');
        tr = table.getElementsByTagName('tr');

        // Loop through all rows, and hide those that don't match the search query
        for (i = 1; i < tr.length; i++) { // Start from 1 to avoid the header row
            tr[i].style.display = 'none'; // Initially hide each row
            td = tr[i].getElementsByTagName('td');
            for (j = 0; j < td.length; j++) {
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = ''; // Show the row if match is found
                        break; // Stop checking other columns if a match is found
                    }
                }
            }
        }
    }
</script>

@endsection
