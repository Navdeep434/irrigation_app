@extends('admin.layouts.app-layout')

@section('page-title', 'User List | Smart Irrigation')

@section('content')
<div class="container">
    <h3 class="custom-header py-1 px-3 mb-4">Users List</h3>
    <hr>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-auto">
            <a href="{{ url()->previous() }}" class="btn custom-header">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>

        <div class="col-md-4 ps-0">
            <div class="input-group glass-input">
                <span class="input-group-text bg-transparent border-0" id="search-addon"><i class="fa fa-search"></i></span>
                <input type="text" class="form-control bg-transparent border-0 text-white shadow-none" id="searchInput" placeholder="Search by name or email...">
            </div>
        </div>

        <div class="col-md-3">
            <div class="input-group glass-input">
                <select class="form-select" id="roleFilter">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="technician">Technician</option>
                    <option value="user">User</option>
                </select>
            </div>
        </div>

        <div class="col-auto ms-auto">
            <a href="{{ route('admin.create-user') }}" class="btn btn-primary">
                <i class="fa fa-plus-circle"></i> Create User
            </a>
        </div>
    </div>

    <hr>
    <div class="table-responsive">
        <table class="table table-bordered glass-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="sortable" data-column="first_name">First Name <i class="sort-icon fa fa-sort"></i></th>
                    <th class="sortable" data-column="last_name">Last Name <i class="sort-icon fa fa-sort"></i></th>
                    <th class="sortable" data-column="email">Email <i class="sort-icon fa fa-sort"></i></th>
                    <th class="sortable" data-column="gender">Gender <i class="sort-icon fa fa-sort"></i></th>
                    <th class="sortable" data-column="dob">DOB <i class="sort-icon fa fa-sort"></i></th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="users-table-body">
                @foreach($users as $user)
                    <tr id="user-row-{{ $user->id }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->first_name }}</td>
                        <td>{{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->gender) }}</td>
                        <td>{{ \Carbon\Carbon::parse($user->dob)->format('F j, Y') }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-primary">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td><span class="badge bg-primary">{{ $user->status }}</span></td>
                        <td>
                            <!-- Edit button only visible if the logged-in user is superadmin or editing own profile -->
                            @if(auth('admin')->id() != $user->id && auth('admin')->user()->hasRole('superadmin'))
                                <a href="{{ route('admin.edit-user', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            @elseif(auth('admin')->id() == $user->id)
                                <a href="{{ route('admin.edit-user', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            @endif
                        
                            @if(auth('admin')->id() != $user->id)
                                <!-- Verify and Unverify buttons -->
                                @if($user->is_verified)
                                    <!-- Unverify button visible for all users except superadmin and logged-in user -->
                                    @if(!in_array('superadmin', $user->roles->pluck('name')->toArray()))
                                        <button type="button" class="btn btn-danger btn-sm unverify-btn"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->first_name }} {{ $user->last_name }}"
                                            data-user-email="{{ $user->email }}"
                                            data-user-contact="+{{$user->country_code}} {{$user->contact_number }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#verifyModal">
                                            Unverify
                                        </button>
                                    @endif
                                @else
                                    <!-- Verify button visible if user is not verified -->
                                    <button type="button" class="btn btn-success btn-sm verify-btn"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->first_name }} {{ $user->last_name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-contact="+{{$user->country_code}} {{$user->contact_number }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#verifyModal">
                                        Verify
                                    </button>
                                @endif
                        
                                <!-- Delete button visible only if the user is not the logged-in user and if the user is not a superadmin -->
                                @if(!in_array('superadmin', $user->roles->pluck('name')->toArray()))
                                    <button type="button" class="btn btn-danger btn-sm delete-btn"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->first_name }} {{ $user->last_name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-contact="+{{$user->country_code}} {{$user->contact_number }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal">
                                        Delete
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="pagination-container">
        {{ $users->links() }}
    </div>
</div>

<!-- VERIFY/UNVERIFY MODAL -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-modal">
            <form id="verifyForm" method="POST">
                @csrf
                <input type="hidden" id="verify_user_id" name="user_id">
                <div class="modal-header glass-header">
                    <h5 class="modal-title">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to <span id="verify_action_text">verify</span> this user?</p>
                    <div class="border rounded p-3">
                        <p><strong>Name:</strong> <span id="modal_user_name"></span></p>
                        <p><strong>Email:</strong> <span id="modal_user_email"></span></p>
                        <p><strong>Contact:</strong> <span id="modal_user_contact"></span></p>
                    </div>
                </div>

                <div class="modal-footer glass-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">OK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content glass-modal">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header glass-header">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                    <div class="border rounded p-3">
                        <p><strong>Name:</strong> <span id="delete_user_name"></span></p>
                        <p><strong>Email:</strong> <span id="delete_user_email"></span></p>
                        <p><strong>Contact:</strong> <span id="delete_user_contact"></span></p>
                    </div>
                </div>

                <div class="modal-footer glass-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const verifyModal = new bootstrap.Modal(document.getElementById('verifyModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        // Verify/Unverify modal
        $(document).on('click', '.verify-btn, .unverify-btn', function () {
            const isVerify = $(this).hasClass('verify-btn');
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            const userEmail = $(this).data('user-email');
            const userContact = $(this).data('user-contact');

            $('#verify_user_id').val(userId);
            $('#modal_user_name').text(userName);
            $('#modal_user_email').text(userEmail);
            $('#modal_user_contact').text(userContact);
            $('#verify_action_text').text(isVerify ? 'verify' : 'unverify');

            const url = '{{ route("admin.verify-user", ":id") }}'.replace(':id', userId);
            $('#verifyForm').attr('action', url);
        });

        $('#verifyForm').on('submit', function (e) {
            e.preventDefault();
            const actionUrl = $(this).attr('action');

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr) {
                    alert('Verification failed');
                }
            });
        });

        // Delete modal
        $(document).on('click', '.delete-btn', function () {
            const userId = $(this).data('user-id');
            const userName = $(this).data('user-name');
            const userEmail = $(this).data('user-email');
            const userContact = $(this).data('user-contact');

            $('#delete_user_name').text(userName);
            $('#delete_user_email').text(userEmail);
            $('#delete_user_contact').text(userContact);

            const url = '{{ route("admin.delete-user", ":id") }}'.replace(':id', userId);
            $('#deleteForm').attr('action', url);
        });

        $('#deleteForm').on('submit', function (e) {
            e.preventDefault();
            const actionUrl = $(this).attr('action');

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr) {
                    alert('Deletion failed');
                }
            });
        });
    });
</script>

@endsection
