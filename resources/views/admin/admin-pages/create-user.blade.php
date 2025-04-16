@extends('admin.layouts.app-layout')

@section('page-title', 'Create User | Smart Irrigation')

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
        <h2 class="custom-header py-1 px-3 mb-3">Create User</h2>
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
                        <!-- Contact Number with Country Code -->
                        <div class="col-md-4 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <div class="input-group">
                                <input type="text" name="country_code" class="form-control" placeholder="+xx" value="{{ old('country_code', '+') }}" required>
                                <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="Phone number" required>
                            </div>
                            @error('country_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @error('contact_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
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

@endsection