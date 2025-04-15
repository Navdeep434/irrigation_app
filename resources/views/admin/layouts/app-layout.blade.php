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
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
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
        }

        .profile-logout a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .profile-logout i {
            margin-right: 10px;
            font-size: 18px;
        }

        .content {
            flex: 1;
            overflow-y: auto;
            padding: 40px;
            color: #333;
        }

        .content::-webkit-scrollbar {
            width: 6px;
        }

        .content::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="glass-sidebar">
        <div class="sidebar-top">
            <div class="d-flex align-items-center mb-4">
                <div class="icon-circle me-3">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h5 class="mb-0 text-white">ADMIN PANEL</h5>
            </div>

            <ul class="nav flex-column gap-2">
                <li class="nav-item">
                    <a href="/admin/dashboard" class="nav-link">
                        <i class="fas fa-chart-line me-2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#administration" role="button" aria-expanded="false">
                        <i class="fas fa-user-plus me-2"></i> Administration
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
                        <i class="fas fa-lock me-2"></i> Roles & Permissions
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
                        <i class="fas fa-cogs me-2"></i> Settings
                    </a>
                </li>
            </ul>
        </div>

        <!-- Profile and Logout -->
        <div class="profile-logout">
            <a href="/admin/profile">
                <i class="fas fa-user-circle"></i> Profile
            </a>
            <a href="/logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        @yield('content')
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
