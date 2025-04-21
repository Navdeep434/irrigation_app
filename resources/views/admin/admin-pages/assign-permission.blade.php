@extends('admin.layouts.app-layout')

@section('page-title', 'Assign Permissions to Role | Smart Irrigation')

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
    <h2 class="custom-header py-1 px-3 mb-4">Assign Permissions to Role</h2>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Role Selection Form -->
    <form method="GET" action="{{ route('admin.roles.assign.permission') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="role_id" class="form-label">Select Role:</label>
                <select name="role_id" id="role_id" class="form-select glass-input" onchange="this.form.submit()" required>
                    <option value="">-- Choose Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ $selectedRoleId == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    @if(isset($selectedRoleId))
    <!-- Permissions Assignment Form -->
    <form action="{{ route('admin.roles.assign.permission.store') }}" method="POST">
        @csrf
        <input type="hidden" name="role_id" value="{{ $selectedRoleId }}">

        <div class="card glass-effect">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Assign Permissions</strong>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-success me-2 select-all">Select All</button>
                    <button type="button" class="btn btn-sm btn-outline-danger deselect-all">Deselect All</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach ($permissions as $permission)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input 
                                    class="form-check-input permission-checkbox" 
                                    type="checkbox" 
                                    name="permissions[]" 
                                    value="{{ $permission->id }}" 
                                    id="permission_{{ $permission->id }}"
                                    {{ in_array($permission->id, $assignedPermissions ?? []) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Assign Permissions</button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

{{-- jQuery CDN and script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        $('.select-all').on('click', function () {
            $('.permission-checkbox').prop('checked', true);
        });

        $('.deselect-all').on('click', function () {
            $('.permission-checkbox').prop('checked', false);
        });
    });
</script>
@endsection
