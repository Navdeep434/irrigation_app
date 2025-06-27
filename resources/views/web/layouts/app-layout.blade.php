<!-- resources/views/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('page-title', 'Dashboard | Smart Irrigation')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/user/sidebar.css') }}" />
    @stack('styles')
    <style>
        /* Sidebar toggle related CSS */
        .sidebar.collapsed {
            width: 70px !important;
        }
        .content.expanded {
            margin-left: 70px !important;
        }
        /* Hide text when sidebar collapsed */
        .sidebar.collapsed .brand span,
        .sidebar.collapsed .nav-section-title,
        .sidebar.collapsed a span,
        .sidebar.collapsed .user-details {
            display: none !important;
        }
        /* Center icons when sidebar collapsed */
        .sidebar.collapsed a,
        .sidebar.collapsed .user-info {
            justify-content: center !important;
            padding: 15px 0 !important;
        }
        .sidebar.collapsed .toggle-arrow {
            display: none !important;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar Navigation -->
        <div class="sidebar" id="sidebar">
            <div class="brand">
                <i class="fas fa-leaf"></i>
                <span>Green Mesh</span>
            </div>

            <div class="user-info">
                <div class="icon-circle me-3">
                    @if(auth()->user()->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()->profile_image) }}??NA"
                             alt="Profile Image"
                             class="rounded-circle"
                             style="width: 45px; height: 45px; object-fit: cover;">
                    @else
                        <i class="fas fa-user-shield fa-2x"></i>
                    @endif
                </div>
                <div class="user-details">
                    <p class="user-name">{{ optional(Auth::user())->first_name ?? 'No name' }}</p>
                </div>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">MAIN</div>
                <a href="{{ route('user.dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">DEVICE MANAGEMENT</div>

                <a href="#device" data-bs-toggle="collapse" role="button"
                   aria-expanded="{{ request()->is('device*') || request()->is('history') || request()->is('my-device') ? 'true' : 'false' }}"
                   aria-controls="device">
                    <i class="fas fa-microchip"></i> <span>Devices</span>
                    <i class="fas fa-chevron-down ms-auto toggle-arrow"></i>
                </a>

                <div class="collapse {{ request()->is('device*') || request()->is('history') || request()->is('my-device') ? 'show' : '' }}" id="device">
                    <a href="{{ route('my.devices') }}" class="{{ request()->is('my-device') ? 'active' : '' }}">
                        <i class="fas fa-hdd"></i> <span>My Devices</span>
                    </a>
                    <a href="{{ route('device.control') }}" class="{{ request()->is('device-control') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h"></i> <span>Device Control</span>
                    </a>
                    <a href="" class="{{ request()->is('history') ? 'active' : '' }}">
                        <i class="fas fa-history"></i> <span>History</span>
                    </a>
                </div>

                <a href="" class="{{ request()->is('schedules') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> <span>Schedules</span>
                </a>

                <a href="" class="{{ request()->is('analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> <span>Analytics</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">SETTINGS</div>
                <a href="" class="{{ request()->is('notifications') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> <span>Notifications</span>
                </a>
                <a href="" class="{{ request()->is('settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> <span>Settings</span>
                </a>
                <a href="" class="{{ request()->is('help') ? 'active' : '' }}">
                    <i class="fas fa-question-circle"></i> <span>Help Center</span>
                </a>
            </div>

            <!-- User Info & Logout -->
            <div class="sidebar-footer">
                <a href="" class="{{ request()->is('profile') ? 'active' : '' }}">
                    <i class="fas fa-user-circle"></i> <span>Profile</span>
                </a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="content">
            <!-- Top Navigation Bar -->
            <div class="top-navbar">
                <button class="nav-toggler" id="sidebarToggler">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-header', 'Dashboard')</h1>
                <div class="navbar-actions">
                    <div class="notif-badge">
                        <i class="fas fa-bell"></i>
                        <span class="badge-count">3</span>
                    </div>
                    <div class="notif-badge">
                        <i class="fas fa-envelope"></i>
                        <span class="badge-count">2</span>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggler = document.getElementById('sidebarToggler');
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content');

            toggler.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('expanded');
            });
        });
    </script>
</body>
</html>
