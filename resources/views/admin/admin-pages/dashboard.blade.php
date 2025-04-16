@extends('admin.layouts.app-layout')

@section('page-title', 'Admin Dashboard | Smart Irrigation')

@section('content')
<main class="p-4">
    <h2 class="custom-header py-1 px-3 mb-4">Welcome, {{ $user->first_name }}!</h2>
    
    <div class="row row-cols-1 row-cols-md-5 g-4">
        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Total Members</h5>
                    <p class="card-text fs-3 text-primary">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Active Members</h5>
                    <p class="card-text fs-3 text-success">{{ $activeUsers }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Inactive Members</h5>
                    <p class="card-text fs-3 text-danger">{{ $inactiveUsers }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Verified Members</h5>
                    <p class="card-text fs-3 text-purple">{{ $verifiedUsers }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Deleted Members</h5>
                    <p class="card-text fs-3 text-danger">{{ $trashedUsers }}</p>
                </div>
            </div>
        </div>
    </div>

    <hr> <!-- Line separator between rows -->

    <!-- New Row Starts Here -->
    <div class="row row-cols-1 row-cols-md-5 g-4">
        
        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Superadmins</h5>
                    <p class="card-text fs-3 text-danger">{{ $superadmins }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Admins</h5>
                    <p class="card-text fs-3 text-info">{{ $admins }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Users</h5>
                    <p class="card-text fs-3 text-secondary">{{ $users }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Technicians</h5>
                    <p class="card-text fs-3 text-warning">{{ $technicians }}</p>
                </div>
            </div>
        </div>
    </div>
    <hr>
</main>
@endsection
