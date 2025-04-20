@extends('admin.layouts.app-layout')
@section('page-title', 'Role List | Smart Irrigation')
@section('content')
<div class="container">
    <h3 class="custom-header py-1 px-3 mb-4">Roles List</h3>
    <hr>

    {{-- Back, Search, Create Button Row --}}
    <div class="row align-items-center mb-3">
        <div class="col-auto ">
            <a href="{{ url()->previous() }}" class="btn custom-header">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="col-md-4 ps-0">
            <div class="input-group glass-input">
                <span class="input-group-text bg-transparent border-0">
                    <i class="fa fa-search"></i>
                </span>
                <input type="text" class="form-control bg-transparent border-0 text-white shadow-none" id="searchRoleInput" placeholder="Search by role name...">
            </div>
        </div>

        <div class="col text-end">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fa fa-plus-circle"></i> Create Role
            </a>
        </div>
    </div>
    <hr>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered glass-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="sortable" data-column="name">Name <i class="sort-icon fa fa-sort"></i></th>
                    <th>Guard</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="roles-table-body">
                @foreach($roles as $role)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $role->name }}</td>
                <td>{{ $role->guard_name }}</td>
                <td>
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning btn-sm">Edit</a>

                    <!-- Check if the role is not superadmin and is not attached to users -->
                    @if($role->name !== 'superadmin' && $role->users()->count() === 0)
                        <button type="button" class="btn btn-danger btn-sm delete-role-btn"
                            data-role-id="{{ $role->id }}"
                            data-role-name="{{ $role->name }}"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteRoleModal">
                            Delete
                        </button>
                    @else
                        <button type="button" class="btn btn-danger btn-sm" disabled>Delete</button>
                    @endif
                </td>
            </tr>
            @endforeach

            </tbody>
        </table>
    </div>

    <div id="pagination-container">
        {{-- {{ $roles->links() }} --}}
        {{ $roles->links('pagination.bootstrap-5') }}
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteRoleForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content glass-effect">
                <div class="modal-header custom-header-color">
                    <h5 class="modal-title" id="deleteRoleModalLabel">Delete Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the role: <strong class="text-danger " id="roleNameToDelete"></strong>?
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

        // Search Roles
        $('#searchRoleInput').on('input', function () {
            const search = $(this).val();
            fetchRoles(search, sortColumn, sortDirection);
        });

        $('.sortable').on('click', function () {
            const column = $(this).data('column');
            sortDirection = (sortColumn === column && sortDirection === 'asc') ? 'desc' : 'asc';
            sortColumn = column;

            $('.sort-icon').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
            const icon = $(this).find('.sort-icon');
            icon.removeClass('fa-sort').addClass(sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down');

            fetchRoles($('#searchRoleInput').val(), sortColumn, sortDirection);
        });

        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            fetchRoles($('#searchRoleInput').val(), sortColumn, sortDirection, $(this).attr('href'));
        });

        function fetchRoles(search, sort, direction, url = "{{ route('admin.roles.list') }}") {
            $.ajax({
                url: url,
                data: { search, sort, direction },
                success: function (data) {
                    $('#roles-table-body').html($(data).find('#roles-table-body').html());
                    $('#pagination-container').html($(data).find('#pagination-container').html());
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }

        // Handle Delete Modal Population
        $(document).on('click', '.delete-role-btn', function () {
            const roleId = $(this).data('role-id');
            const roleName = $(this).data('role-name');

            $('#roleNameToDelete').text(roleName);
            $('#deleteRoleForm').attr('action', /admin/roles/delete/${roleId});
        });

        // Handle Role Deletion with AJAX
        $('#deleteRoleForm').on('submit', function (e) {
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
                        $('#deleteRoleModal').modal('hide');
                        // Remove the deleted row from the table
                        $([data-role-id="${response.role_id}"]).closest('tr').remove();
                    }
                    alert(response.message);
                },
                error: function (xhr) {
                    alert('An error occurred while deleting the role.');
                }
            });
        });
    });
</script>
@endsection