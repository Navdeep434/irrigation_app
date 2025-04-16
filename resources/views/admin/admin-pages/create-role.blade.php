@extends('admin.layouts.app-layout')

@section('page-title', 'Create Role | Smart Irrigation')

@section('content')
<div class="container mt-4">
    
    <div class="row align-items-center mb-3">
        <div class="col-auto ">
            <a href="{{ url()->previous() }}" class="btn custom-header">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <hr>
    <h2 class="custom-header py-1 px-3 mb-4">Create Role</h2>
    <hr>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card glass-effect">
        <div class="card-header">
            <strong>New Role</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Role Name -->
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Guard Name -->
                    <div class="col-md-6 mb-3">
                        <label for="guard_name" class="form-label">Guard</label>
                        <select class="form-select" id="guard_name" name="guard_name" required>
                            <option value="web" {{ old('guard_name') == 'web' ? 'selected' : '' }}>Web</option>
                            <option value="admin" {{ old('guard_name') == 'admin' ? 'selected' : '' }}>Admin</option>
                            {{-- <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>Api</option> --}}
                        </select>
                        @error('guard_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create Role</button>
            </form>
        </div>
    </div>
</div>

@endsection
