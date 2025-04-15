@extends('admin.layouts.app-layout')

@section('page-title', 'Admin Dashboard | Smart Irrigation')

@section('content')
<style>
    .custom-header {
            /* border-left: 4px solid #ffffff;
            border-top: 1px solid #ffffff;
            border-bottom: 1px solid #ffffff; */
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
            color: #000;
            border-radius: 5px;
        }
</style>
<main class="p-4">
    <h2 class="custom-header py-1 px-3 mb-4">Welcome, {{ $user->first_name }}!</h2>
    <hr>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5 g-4">
        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text fs-3 text-primary">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Active Users</h5>
                    <p class="card-text fs-3 text-success">{{ $activeUsers }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Inactive Users</h5>
                    <p class="card-text fs-3 text-danger">{{ $inactiveUsers }}</p>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title">Verified Users</h5>
                    <p class="card-text fs-3 text-purple">{{ $verifiedUsers }}</p>
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
