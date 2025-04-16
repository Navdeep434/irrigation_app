@extends('admin.layouts.app-layout')
@section('page-title', 'List Permissions | Smart Irrigation')
@section('content')
<div class="container mt-4">
    <h2 class="custom-header py-1 px-3 mb-4">List of Permissions</h2>
    <hr>
    <div class="row align-items-center mb-3">
        <div class="col-auto">
            <a href="{{ url()->previous() }}" class="btn custom-header">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="col-md-4 ps-0">
            <div class="input-group glass-input">
                <span class="input-group-text bg-transparent border-0">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" class="form-control bg-transparent border-0 text-white shadow-none" id="searchPermissionInput" placeholder="Search Permissions" />
            </div>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('admin.permission.create') }}" class="btn custom-header">
                <i class="fa fa-plus"></i> Create New Permission
            </a>
        </div>
    </div>
    <hr>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card glass-effect">
        <div class="card-header">
            <strong>Permissions</strong>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Permission Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="permissions-table-body">
                    @foreach($permissions as $permission)
                    <tr id="permission-{{ $permission->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>
                            <a href="{{ route('admin.permission.edit', $permission->id) }}" class="btn btn-sm btn-warning">Edit</a>

                            <button type="button" class="btn btn-sm btn-danger delete-permission-btn"
                                data-permission-id="{{ $permission->id }}"
                                data-permission-name="{{ $permission->name }}"
                                data-bs-toggle="modal"
                                data-bs-target="#deletePermissionModal">
                                Delete
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination Links -->
            {{ $permissions->links() }}
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deletePermissionModal" tabindex="-1" aria-labelledby="deletePermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deletePermissionForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content glass-effect">
                <div class="modal-header custom-header-color">
                    <h5 class="modal-title" id="deletePermissionModalLabel">Delete Permission</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the permission: <strong class="text-danger " id="permissionNameToDelete"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let sortColumn = 'name';
        let sortDirection = 'asc';

        // Search Permissions
        $('#searchPermissionInput').on('input', function () {
            const search = $(this).val();
            fetchPermissions(search, sortColumn, sortDirection);
        });

        // Sort and Pagination
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            fetchPermissions($('#searchPermissionInput').val(), sortColumn, sortDirection, $(this).attr('href'));
        });

        function fetchPermissions(search, sort, direction, url = "{{ route('admin.permission.list') }}") {
            $.ajax({
                url: url,
                data: { search, sort, direction },
                success: function (data) {
                    $('#permissions-table-body').html($(data).find('#permissions-table-body').html());
                    $('#pagination-container').html($(data).find('#pagination-container').html());
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Handle Delete Modal Population
        $(document).on('click', '.delete-permission-btn', function () {
            const permissionId = $(this).data('permission-id');
            const permissionName = $(this).data('permission-name');

            $('#permissionNameToDelete').text(permissionName);
            $('#deletePermissionForm').attr('action', `/admin/permissions/delete/${permissionId}`);
        });

        // Handle Permission Deletion with AJAX
        $('#deletePermissionForm').on('submit', function (e) {
            e.preventDefault();

            const actionUrl = $(this).attr('action');
            const formData = $(this).serialize();

            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        // Close the modal
                        $('#deletePermissionModal').modal('hide');
                        // Remove the deleted row from the table
                        $(`#permission-${response.permission_id}`).remove();
                    }
                    alert(response.message);
                },
                error: function (xhr) {
                    alert('An error occurred while deleting the permission.');
                }
            });
        });
    });
</script>


@endsection
