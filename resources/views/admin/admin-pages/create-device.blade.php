@extends('admin.layouts.app-layout')

@section('page-title', 'Create Device | Smart Irrigation')

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
    <h2 class="custom-header py-1 px-3 mb-4">Create Device</h2>
    <hr>

    <div class="card glass-effect shadow rounded-3">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Add New Device</h4>
        </div>

        <div class="card-body">
            <form id="device-form">
                @csrf

                <div class="mb-3">
                    <label for="total_valves" class="form-label">Total Valves</label>
                    <input type="number" name="total_valves" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="total_flow_sensors" class="form-label">Total Flow Sensors</label>
                    <input type="number" name="total_flow_sensors" class="form-control" min="0" max="10" required>
                </div>

                <div class="mb-3">
                    <label for="total_temp_sensors" class="form-label">Total Temperature Sensors</label>
                    <input type="number" name="total_temp_sensors" class="form-control" value="1" readonly>
                </div>

                {{-- <div class="mb-3">
                    <label for="in_repair" class="form-label">In Repair</label>
                    <select name="in_repair" class="form-select" required>
                        <option value="0" selected>No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="is_blocked" class="form-label">Is Blocked</label>
                    <select name="is_blocked" class="form-select" required>
                        <option value="0" selected>No</option>
                        <option value="1">Yes</option>
                    </select>
                </div> --}}

                <div class="text-end">
                    <button type="submit" class="btn btn-success">Create Device</button>
                </div>
            </form>

            <div id="success-message" class="alert alert-success mt-3 d-none"></div>
        </div>
    </div>
</div>

{{-- Ensure jQuery is available --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#device-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.devices.store') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                },
                success: function(response) {
                    $('#success-message').removeClass('d-none').text(response.message);
                    $('#device-form')[0].reset();
                },
                error: function(xhr) {
                    let errorText = 'Something went wrong.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorText = Object.values(errors).flat().join(' ');
                    }
                    alert(errorText);
                }
            });
        });
    });
</script>
@endsection
