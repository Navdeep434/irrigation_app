<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Role List | Smart Irrigation')</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/common.css') }}">
</head>
<body>

    <!-- Sidebar -->
    <div class="glass-sidebar" id="sidebar">
        <button class="toggle-sidebar" id="toggleSidebar">
            <i class="fas fa-chevron-left" id="toggleIcon"></i>
        </button>
        
        <div class="sidebar-top">
            <div class="d-flex align-items-center mb-4">
                <div class="icon-circle me-3">
                    @if(auth()->user()->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" 
                             alt="Profile Image" 
                             class="rounded-circle" 
                             style="width: 45px; height: 45px; object-fit: cover;">
                    @else
                        <i class="fas fa-user-shield"></i>
                    @endif
                </div>
                
                <h5 class="mb-0 text-white sidebar-text">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h5>
            </div>

            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a href="/admin/dashboard" class="nav-link">
                        <i class="fas fa-chart-line me-2"></i> <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#administration" role="button" aria-expanded="false">
                        <i class="fas fa-user-plus me-2"></i> <span class="sidebar-text">Administration</span>
                    </a>
                    <div class="collapse" id="administration">
                        <a href="/admin/create-user" class="nav-link">Create User</a>
                    </div>
                    <div class="collapse" id="administration">
                        <a href="{{route('admin.list-users')}}" class="nav-link">Users List</a>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#rolesPermissions" role="button" aria-expanded="false">
                        <i class="fas fa-lock me-2"></i> <span class="sidebar-text">Roles & Permissions</span>
                    </a>
                    <div class="collapse" id="rolesPermissions">
                        <a href="{{route('admin.roles.create')}}" class="nav-link">Create Role</a>
                        <a href="{{route('admin.roles.list')}}" class="nav-link">List Roles</a>
                        <a href="{{route('admin.permission.create')}}" class="nav-link">Create Permission</a>
                        <a href="{{route('admin.permission.list')}}" class="nav-link">List Permissions</a>
                        <a href="{{route('admin.roles.assign.permission')}}" class="nav-link">Assign Permissions</a>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="{{route('admin.settings')}}" class="nav-link">
                        <i class="fas fa-cogs me-2"></i> <span class="sidebar-text">Settings</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Profile and Logout -->
        <div class="profile-logout">
            <a href="/admin/profile">
                <i class="fas fa-user-circle"></i> <span class="sidebar-text">Profile</span>
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> <span class="sidebar-text">Logout</span>
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="content" id="content">
        @yield('content')
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script -->
    <script>
        $(document).ready(function() {
            $('#toggleSidebar').click(function() {
                $('#sidebar').toggleClass('sidebar-collapsed');
                
                // Toggle icon
                if ($('#sidebar').hasClass('sidebar-collapsed')) {
                    $('#toggleIcon').removeClass('fa-chevron-left').addClass('fa-chevron-right');
                } else {
                    $('#toggleIcon').removeClass('fa-chevron-right').addClass('fa-chevron-left');
                }
                
                // Save state to localStorage
                localStorage.setItem('sidebarCollapsed', $('#sidebar').hasClass('sidebar-collapsed'));
            });
            
            // Check if sidebar was collapsed previously
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                $('#sidebar').addClass('sidebar-collapsed');
                $('#toggleIcon').removeClass('fa-chevron-left').addClass('fa-chevron-right');
            }
        });
    </script>
</body>
</html>