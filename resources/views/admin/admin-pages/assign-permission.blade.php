@extends('admin.layouts.app-layout')

@section('title', 'Assign Permissions to Role')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="bg-white shadow-md rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Assign Permissions to Role</h2>

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.roles.assign.permission.store') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Select Role:</label>
                <select name="role_id" id="role" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    <option value="">-- Choose Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5">
                <label for="permissions" class="block text-sm font-semibold text-gray-700 mb-2">Select Permissions:</label>
                <select name="permissions[]" id="permissions" multiple class="w-full px-4 py-2 border rounded-lg h-52 focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    @foreach ($permissions as $permission)
                        <option value="{{ $permission->id }}">{{ $permission->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Hold <kbd>Ctrl</kbd> (Windows) or <kbd>Cmd</kbd> (Mac) to select multiple</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow">
                    Assign Permissions
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
