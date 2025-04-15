<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Smart Irrigation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
            height: 100vh;
            position: fixed;
        }
        .sidebar a {
            color: #ffffff;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar .collapse a {
            padding-left: 30px;
        }
        .content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 20px;
        }
        .sidebar-footer {
            position: absolute;
            bottom: 20px;
            width: 100%;
        }
        .sidebar-footer a {
            color: #ffffff;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
        }
        .sidebar-footer a:hover {
            background-color: #495057;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="/dashboard">Dashboard</a>

        <!-- Device Section -->
        <a data-bs-toggle="collapse" href="#device" role="button" aria-expanded="false" aria-controls="device">Device</a>
        <div class="collapse ms-3" id="device">
            <a href="/device-control">Device Control</a>
            <a href="/device">Devices</a>
            <a href="/history">History</a>
            <a href="/my-device">My Devices</a>
        </div>

        <!-- Profile Section -->
        <div class="profile-logout">
            <a href="/admin/profile">
                <i class="fas fa-user-circle"></i> Profile
            </a>
            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
