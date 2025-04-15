@extends('web.layouts.app-layout') 

@section('page-title', 'Dashboard | Smart Irrigation')

@section('content')
<main class="p-4">
    <h2 class="mb-3">Welcome, {{ $user->first_name }}!</h2>

    <div class="card shadow-sm p-4">
        <h5>User Dashboard</h5>
        <p>You are logged in as a <strong>{{ $user->role }}</strong>.</p>

    </div>
</main>
@endsection
