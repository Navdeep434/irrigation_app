<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smart Irrigation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
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
        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="/admin/dashboard">Dashboard</a>
        <a data-bs-toggle="collapse" href="#administration" role="button" aria-expanded="false" aria-controls="administration">Administration</a>
        <div class="collapse ms-3" id="administration">
            <a href="">Create User</a>
        </div>
        <a data-bs-toggle="collapse" href="#rolesPermissions" role="button" aria-expanded="false" aria-controls="rolesPermissions">Roles & Permissions</a>
        <div class="collapse ms-3" id="rolesPermissions">
            <a href="/admin/roles/create">Create Role</a>
            <a href="/admin/roles">List Roles</a>
            <a href="/admin/permissions/create">Create Permission</a>
            <a href="/admin/permissions">List Permissions</a>
            <a href="/admin/assign-permission">Assign Permissions to Role</a>
        </div>
    </div>

    <div class="content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
