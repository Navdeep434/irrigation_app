<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Smart Irrigation')</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            display: flex;
            margin: 0;
            height: 100vh;
            overflow: hidden;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
        }

        .glass-sidebar {
            width: 270px;
            background: rgba(31, 38, 45, 0.6);
            backdrop-filter: blur(12px);
            color: #ffffff;
            padding: 30px 20px;
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 100;
        }

        .sidebar-collapsed {
            width: 80px;
            padding: 30px 10px;
        }

        .sidebar-collapsed .sidebar-text,
        .sidebar-collapsed .collapse,
        .sidebar-collapsed h5 {
            display: none;
        }

        .sidebar-collapsed .nav-link {
            text-align: center;
            padding: 5px 5px;
        }

        .sidebar-collapsed .nav-link i {
            margin-right: 0 !important;
            font-size: 15px;
        }

        .sidebar-collapsed .icon-circle {
            margin: 0 auto !important;
            margin-bottom: 20px !important;
        }

        .icon-circle {
            width: 50px;
            height: 50px;
            background-color: #17a2b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-circle i {
            color: #fff;
            font-size: 20px;
        }

        .nav-link {
            color: #e9ecef;
            font-size: 16px;
            padding: 10px 15px;
            border-radius: 12px;
            transition: all 0.3s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar-collapsed .nav-link:hover {
            transform: scale(1.1);
        }

        .nav .collapse .nav-link {
            padding-left: 30px;
            font-size: 15px;
        }

        .sidebar-top {
            flex-grow: 1;
        }

        .profile-logout {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
        }

        .profile-logout a {
            display: flex;
            align-items: center;
            color: #ffffff;
            padding: 8px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 15px;
            text-decoration: none;
        }

        .profile-logout a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .profile-logout i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar-collapsed .profile-logout a {
            justify-content: center;
        }

        .sidebar-collapsed .profile-logout i {
            margin-right: 0;
        }

        .content {
            flex: 1;
            overflow-y: auto;
            padding: 40px;
            color: #333;
            transition: all 0.3s ease;
        }

        .content::-webkit-scrollbar {
            width: 6px;
        }

        .content::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 10px;
        }
        
        .toggle-sidebar {
            position: absolute;
            top: 20px;
            right: -15px;
            width: 30px;
            height: 30px;
            background-color: #17a2b8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 101;
            border: none;
            transition: all 0.3s ease;
        }
        
        .toggle-sidebar:hover {
            background-color: #138496;
            transform: scale(1.1);
        }
    </style>
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
                    <i class="fas fa-user-shield"></i>
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
                        <a href="/admin/roles/create" class="nav-link">Create Role</a>
                        <a href="/admin/roles" class="nav-link">List Roles</a>
                        <a href="/admin/permissions/create" class="nav-link">Create Permission</a>
                        <a href="/admin/permissions" class="nav-link">List Permissions</a>
                        <a href="/admin/assign-permission" class="nav-link">Assign Permissions</a>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="/admin/settings" class="nav-link">
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