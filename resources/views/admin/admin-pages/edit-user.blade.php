@extends('admin.layouts.app-layout')

@section('page-title', 'Edit User | Smart Irrigation')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">Edit User</h3>

        <!-- Show Success or Error Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Card to contain the form with glass effect -->
        <div class="card glass-effect">
            <div class="card-header">
                <strong>Edit User Details</strong>
            </div>
            <div class="card-body">
                <!-- Edit User Form -->
                <form action="{{ route('admin.update-user', $user->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <!-- First Name and Last Name -->
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Gender and Date of Birth -->
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob', $user->dob) }}" required>
                        </div>

                        <!-- Show Role Dropdown Only If User is Not a Superadmin -->
                        @if(!$user->hasRole('superadmin'))
                            <div class="col-md-4 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" class="form-control" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst($role->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <!-- Password and Confirm Password -->
                        <div class="col-md-4 mb-3">
                            <label for="password" class="form-label">Password (Leave blank to keep unchanged)</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep unchanged">
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="col-md-4 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update User</button>
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

        /* Optional: Adjust form inputs */
        .form-control {
            background-color: rgba(255, 255, 255, 0.7); /* Add some transparency to the inputs */
        }
    </style>
@endsection
