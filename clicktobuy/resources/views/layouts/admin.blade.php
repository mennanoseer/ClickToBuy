<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ClickToBuy') }} - Admin</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js', 'resources/css/admin.css'])
    
    <!-- Additional CSS -->
    <link href="{{ asset('css/admin-notifications.css') }}" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom styles for horizontal layout -->
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f9fc;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .content-wrapper {
            padding: 25px;
        }
        .nav-section {
            font-weight: bold;
            color: rgba(255, 255, 255, 0.8);
        }
        .notification-item.unread {
            background-color: rgba(13, 110, 253, 0.1);
            font-weight: bold;
        }
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: white;
        }
        .notifications-container {
            max-height: 300px;
            overflow-y: auto;
        }
        .badge-counter {
            position: absolute;
            transform: scale(0.7);
            transform-origin: top right;
            top: 0.25rem;
            right: 0.25rem;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Horizontal Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-store me-2"></i>
                CLICKTOBUY ADMIN
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                            <i class="fas fa-box me-1"></i>
                            Products
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                            <i class="fas fa-folder me-1"></i>
                            Categories
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart me-1"></i>
                            Orders
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                            <i class="fas fa-users me-1"></i>
                            Customers
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                            <i class="fas fa-star me-1"></i>
                            Reviews
                        </a>
                    </li>
                </ul>
                
                <!-- Right side of navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" id="notificationDropdownToggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="badge bg-danger rounded-pill badge-counter">{{ Auth::user()->unreadNotifications->count() }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdownToggle" style="width: 300px;">
                            <li>
                                <div class="d-flex justify-content-between align-items-center px-3 py-2">
                                    <h6 class="dropdown-header p-0 m-0">Notifications</h6>
                                    <a href="#" class="mark-all-read text-decoration-none small">Mark All as Read</a>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider my-0"></li>
                            <div class="notifications-container">
                                @forelse(Auth::user()->notifications->take(5) as $notification)
                                    <li>
                                        <a href="{{ $notification->data['link'] ?? '#' }}" class="dropdown-item notification-item {{ $notification->read_at ? '' : 'unread' }}" data-id="{{ $notification->id }}">
                                            <div class="d-flex align-items-center">
                                                <div class="notification-icon {{ $notification->data['icon_class'] ?? 'bg-primary' }} me-3">
                                                    <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                                                </div>
                                                <div class="notification-content flex-grow-1">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">{{ $notification->data['title'] }}</h6>
                                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                    <p class="mb-1 small">{{ Str::limit($notification->data['message'], 100) }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li><span class="dropdown-item text-center py-3">No notifications</span></li>
                                @endforelse
                            </div>
                            <li><hr class="dropdown-divider my-0"></li>
                            <li><a href="{{ route('admin.notifications.index') }}" class="dropdown-item text-center">View All Notifications</a></li>
                        </ul>
                    </li>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdownToggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            {{ Auth::user()->user_name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a href="{{ route('home') }}" class="dropdown-item">
                                    <i class="fas fa-store fa-sm fa-fw me-2"></i>
                                    View Store
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2"></i>
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="content-wrapper">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')

        <!-- Footer -->
        <footer class="mt-5 pt-4 border-top text-center">
            <p>Copyright &copy; ClickToBuy {{ date('Y') }}</p>
        </footer>
    </div>

    <!-- Dashboard Charts JS -->
    @if(request()->routeIs('admin.dashboard'))
    <script src="{{ asset('js/admin-dashboard-charts.js') }}"></script>
    @endif
    
    <!-- Admin Notifications JS -->
    <script src="{{ asset('js/admin-notifications.js') }}"></script>
    
    @yield('scripts')
</body>
</html> 