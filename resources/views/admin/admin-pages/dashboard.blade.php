@extends('admin.layouts.app-layout') 

@section('page-title', 'Admin Dashboard | Smart Irrigation')

@section('content')
<main class="p-4">
    <h2 class="mb-3">Welcome, {{ $user->first_name }}!</h2>

    <div class="card shadow-sm p-4">
        <h5>Superadmin Dashboard</h5>
        <p>You are logged in as a <strong>{{ $user->role }}</strong>.</p>

        {{-- Add dashboard features here --}}
        <div class="mt-3">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</main>
@endsection
