@extends('admin.layouts.app-layout')

@section('page-title', 'Create User | Smart Irrigation')

@section('content')
    <div class="container mt-4">
        <h2>Create User</h2>
        <hr> <!-- Horizontal line below the title -->

        <!-- Success or Error Message -->
        @if(session('success') || session('message'))
            <div class="alert alert-success">
                {{ session('success') ?? session('message') }}
            </div>
        @endif

        <!-- Card to contain the form -->
        <div class="card glass-effect">
            <div class="card-header">
                <strong>Create a new user</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.store-user') }}" method="POST">
                    @csrf

                    <div class="row">
                        <!-- First Name and Last Name -->
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                            @error('first_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            @error('last_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Gender and Date of Birth -->
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}" required>
                            @error('dob')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Role -->
                        <div class="col-md-4 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select name="role" class="form-control" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Password and Confirm Password -->
                        <div class="col-md-4 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            @error('password')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- Confirm Password -->
                        <div class="col-md-4 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create User</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Glass Effect for Card */
        .glass-effect {
            background: rgba(255, 255, 255, 0.3); /* semi-transparent background */
            backdrop-filter: blur(10px); /* blur the background */
            border-radius: 10px; /* rounded corners */
            border: 1px solid rgba(255, 255, 255, 0.2); /* subtle border */
            padding: 20px;
        }

        /* Optional: Adding shadow effect for better visibility */
        .glass-effect .card-header {
            background: rgba(255, 255, 255, 0); /* light header background */
            backdrop-filter: blur(10px); /* apply blur effect */
            border-radius: 10px;
        }

        /* Optional: Add some extra style to the card body */
        .glass-effect .card-body {
            background: rgba(255, 255, 255, 0); /* light background for card body */
            backdrop-filter: blur(8px); /* apply blur effect */
            border-radius: 10px;
        }
    </style>
@endsection
