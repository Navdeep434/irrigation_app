@extends('admin.layouts.app-layout')

@section('page-title', 'Edit Permission | Smart Irrigation')

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
    <h2 class="custom-header py-1 px-3 mb-4">Edit Permission</h2>
    <hr>

    <div class="card glass-effect">
        <div class="card-header">
            <strong>Edit Permission</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.permission.update', $permission->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Permission Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $permission->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Permission</button>
            </form>
        </div>
    </div>
</div>
@endsection
