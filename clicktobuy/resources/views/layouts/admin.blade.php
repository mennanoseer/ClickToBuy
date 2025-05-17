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

    <!-- Styles -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
    <style>
        /* Custom admin styles */
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #4e73df;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            background-size: cover;
        }
        
        .sidebar-brand {
            height: 4.375rem;
            font-size: 1rem;
            font-weight: 800;
            padding: 1.5rem 1rem;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
        }
        
        .sidebar-brand-icon {
            font-size: 2rem;
        }
        
        .sidebar-brand-text {
            display: inline;
            margin-left: 0.5rem;
        }
        
        .sidebar hr {
            margin: 0 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .sidebar .nav-item {
            position: relative;
        }
        
        .sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 700;
            padding: 0.75rem 1rem;
        }
        
        .sidebar .nav-item .nav-link:hover {
            color: #fff;
        }
        
        .sidebar .nav-item .nav-link i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .sidebar .nav-item .nav-link.active {
            color: #fff;
            font-weight: 700;
        }
        
        .sidebar-divider {
            margin: 0 1rem 1rem;
        }
        
        .sidebar-heading {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            font-weight: 800;
            padding: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.1rem;
        }
        
        .content-wrapper {
            background-color: #f8f9fc;
            width: 100%;
            overflow-x: hidden;
        }
        
        .topbar {
            height: 4.375rem;
        }
        
        .topbar .navbar {
            height: 4.375rem;
            padding: 0 1rem;
        }
        
        .topbar .navbar-search {
            width: 25rem;
        }
        
        .topbar .nav-item .nav-link {
            height: 4.375rem;
            display: flex;
            align-items: center;
            padding: 0 0.75rem;
        }
        
        .topbar .nav-item .nav-link .badge-counter {
            position: absolute;
            transform: scale(0.7);
            transform-origin: top right;
            right: 0.25rem;
            margin-top: -0.25rem;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('admin.dashboard') }}">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-store"></i>
                </div>
                <div class="sidebar-brand-text mx-3">ClickToBuy Admin</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Catalog
            </div>

            <!-- Nav Item - Products -->
            <li class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.products.index') }}">
                    <i class="fas fa-fw fa-box"></i>
                    <span>Products</span>
                </a>
            </li>

            <!-- Nav Item - Categories -->
            <li class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.categories.index') }}">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Categories</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Sales
            </div>

            <!-- Nav Item - Orders -->
            <li class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.orders.index') }}">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>

            <!-- Nav Item - Customers -->
            <li class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.customers.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Content
            </div>

            <!-- Nav Item - Reviews -->
            <li class="nav-item {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.reviews.index') }}">
                    <i class="fas fa-fw fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->user_name }}</span>
                                <i class="fas fa-user fa-fw"></i>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('home') }}">
                                    <i class="fas fa-store fa-sm fa-fw mr-2 text-gray-400"></i>
                                    View Store
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; ClickToBuy {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>

    <script>
        // Toggle the side navigation
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.body.classList.toggle('sidebar-toggled');
                    document.querySelector('.sidebar').classList.toggle('toggled');
                });
            }
        });
    </script>
</body>
</html>
