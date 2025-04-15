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
            <!-- Search Input -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text" id="search-addon"><i class="fa fa-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name or email...">
                </div>
            </div>

            <!-- Role Filter Dropdown -->
            <div class="col-md-4">
                <div class="input-group">
                    <select class="form-select" id="roleFilter" aria-label="Filter by Role">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="technician">Technician</option>
                        <option value="user">User</option>
                    </select>
                </div>
            </div>

            <!-- Create User Button -->
            <div class="col-md-4 text-end">
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
                        <th class="sortable" data-column="dob">Date of Birth <i class="sort-icon fa fa-sort"></i></th>
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
                                <a href="{{ route('admin.edit-user', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                @if(auth('admin')->id() != $user->id)
                                    <!-- Check if user is verified -->
                                    @if($user->is_verified)
                                        <button type="button" class="btn btn-danger btn-sm unverify-btn"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->first_name }} {{ $user->last_name }}"
                                                data-user-email="{{ $user->email }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#verifyModal">
                                            Unverify
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success btn-sm verify-btn"
                                                data-user-id="{{ $user->id }}"
                                                data-user-name="{{ $user->first_name }} {{ $user->last_name }}"
                                                data-user-email="{{ $user->email }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#verifyModal">
                                            Verify
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.delete-user', $user->id) }}" class="btn btn-danger btn-sm">Delete</a>
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

    <!-- Verify User Modal -->
    <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="verifyForm" method="POST">
                    @csrf
                    <input type="hidden" id="verify_user_id" name="user_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="verifyModalLabel">Verify User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p>Are you sure you want to verify this user?</p>
                        <div class="border rounded p-3">
                            <p><strong>Name:</strong> <span id="modal_user_name"></span></p>
                            <p><strong>Email:</strong> <span id="modal_user_email"></span></p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">OK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add CSS for sorting styles -->
    <style>
        .sortable {
            cursor: pointer;
        }
        .sort-icon {
            margin-left: 5px;
        }
        .fa-sort-up, .fa-sort-down {
            color: #007bff;
        }
        .glass-table {
            background: rgba(255, 255, 255, 0.15);
            /* border-radius: 1rem; */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.164);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
        }
    
        .glass-table thead th {
            background-color: rgba(255, 255, 255, 0.25);
            color: #000;
        }
    
        .glass-table tbody tr {
            background-color: rgba(255, 255, 255, 0.1);
            color: #000;
        }
    
        .glass-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
    
        .glass-modal {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 1rem;
        }
    
        .modal-content.glass-modal {
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
        }
    
        /* Optional for overall background */
        body {
            background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
            background-attachment: fixed;
        }
        .custom-header {
            /* border-left: 4px solid #ffffff;
            border-top: 1px solid #ffffff;
            border-bottom: 1px solid #ffffff; */
            background: linear-gradient(to right, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0));
            color: #000;
            border-radius: 10px;
        }
    </style>
    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const verifyModal = new bootstrap.Modal(document.getElementById('verifyModal'));
            let searchTimeout = null;
            let currentSort = {
                column: 'id',
                direction: 'asc'
            };

            // Initialize the verify/unverify modal
            $('.verify-btn, .unverify-btn').on('click', function () {
                const userId = $(this).data('user-id');
                const userName = $(this).data('user-name');
                const userEmail = $(this).data('user-email');
                const isVerifyAction = $(this).hasClass('verify-btn'); // Check if it's a verify action

                $('#verify_user_id').val(userId);
                $('#modal_user_name').text(userName);
                $('#modal_user_email').text(userEmail);

                const actionUrl = '{{ route("admin.verify-user", ":id") }}'.replace(':id', userId);
                $('#verifyForm').attr('action', actionUrl);

                if (isVerifyAction) {
                    $('#verifyForm button[type="submit"]').text('Verify User');
                } else {
                    $('#verifyForm button[type="submit"]').text('Unverify User');
                }
            });

            // Handle the verify/unverify form submission
            $('#verifyForm').on('submit', function (e) {
                e.preventDefault();

                const userId = $('#verify_user_id').val();
                const actionUrl = $(this).attr('action');

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('#verifyForm button[type="submit"]').prop('disabled', true).text('Processing...');
                    },
                    success: function (response) {
                        if (response.success) {
                            $('#user-row-' + userId).addClass('table-success');
                            verifyModal.hide();
                            // alert(response.message);
                            location.reload();
                        } else {
                            // alert('Action failed!');
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        alert('Something went wrong during the verification process.');
                    },
                    complete: function () {
                        $('#verifyForm button[type="submit"]').prop('disabled', false).text('OK');
                    }
                });
            });

            // Search functionality
            $('#searchInput').on('input', function() {
                const searchTerm = $(this).val();
                
                // Clear any existing timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // Set a timeout to avoid too many requests while typing
                searchTimeout = setTimeout(function() {
                    fetchUsers(searchTerm, currentSort.column, currentSort.direction);
                }, 300);
            });

            // Sorting functionality
            $('.sortable').on('click', function() {
                const column = $(this).data('column');
                
                // Toggle direction if clicking the same column
                if (currentSort.column === column) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = column;
                    currentSort.direction = 'asc';
                }
                
                // Update sort icons
                $('.sort-icon').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
                const icon = $(this).find('.sort-icon');
                icon.removeClass('fa-sort');
                icon.addClass(currentSort.direction === 'asc' ? 'fa-sort-up' : 'fa-sort-down');
                
                // Fetch sorted data
                fetchUsers($('#searchInput').val(), currentSort.column, currentSort.direction);
            });

            // Function to fetch users with search and sort
            function fetchUsers(search, sortColumn, sortDirection) {
                $.ajax({
                    url: '{{ route("admin.list-users") }}',
                    type: 'GET',
                    data: {
                        search: search,
                        sort: sortColumn,
                        direction: sortDirection
                    },
                    beforeSend: function() {
                        // Show loading indicator if needed
                    },
                    success: function(response) {
                        $('#users-table-body').html($(response).find('#users-table-body').html());
                        $('#pagination-container').html($(response).find('#pagination-container').html());
                        
                        // Reattach event handlers for the newly loaded buttons
                        $('.verify-btn, .unverify-btn').on('click', function () {
                            const userId = $(this).data('user-id');
                            const userName = $(this).data('user-name');
                            const userEmail = $(this).data('user-email');
                            const isVerifyAction = $(this).hasClass('verify-btn');

                            $('#verify_user_id').val(userId);
                            $('#modal_user_name').text(userName);
                            $('#modal_user_email').text(userEmail);

                            const actionUrl = '{{ route("admin.verify-user", ":id") }}'.replace(':id', userId);
                            $('#verifyForm').attr('action', actionUrl);

                            if (isVerifyAction) {
                                $('#verifyForm button[type="submit"]').text('Verify User');
                            } else {
                                $('#verifyForm button[type="submit"]').text('Unverify User');
                            }
                        });
                    },
                    error: function(xhr) {
                        console.error('Error fetching data:', xhr.responseText);
                    },
                    complete: function() {
                        // Hide loading indicator if needed
                    }
                });
            }

            // Handle pagination clicks
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                
                $.ajax({
                    url: url,
                    data: {
                        search: $('#searchInput').val(),
                        sort: currentSort.column,
                        direction: currentSort.direction
                    },
                    success: function(response) {
                        $('#users-table-body').html($(response).find('#users-table-body').html());
                        $('#pagination-container').html($(response).find('#pagination-container').html());
                        
                        // Reattach event handlers
                        $('.verify-btn, .unverify-btn').on('click', function () {
                            // Same code as above for handling modal
                        });
                    }
                });
            });
        });
    </script>
@endsection