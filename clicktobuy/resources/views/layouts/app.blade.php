<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Click To Buy') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/shopping.css') }}" rel="stylesheet">
    <link href="{{ asset('css/cart-animations.css') }}" rel="stylesheet">
    <link href="{{ asset('css/table-visibility-fix.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <script src="{{ asset('js/shopping.js') }}" defer></script>
    <script src="{{ asset('js/text-visibility-check.js') }}"></script>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Click To Buy') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">{{ __('Products') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('about') }}">{{ __('About') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact') }}">{{ __('Contact') }}</a>
                        </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Cart & Wishlist Links -->
                        <li class="nav-item">
                            <a class="nav-link cart-count" href="{{ route('cart.index') }}">
                                <i class="fas fa-shopping-cart"></i> Cart
                                @auth
                                    @if(Auth::user()->customer && Auth::user()->customer->cart && Auth::user()->customer->cart->cartItems->count() > 0)
                                        <span class="badge bg-danger cart-badge">{{ Auth::user()->customer->cart->cartItems->count() }}</span>
                                    @else
                                        <span class="badge bg-danger cart-badge" style="display: none;">0</span>
                                    @endif
                                @else
                                    <span class="badge bg-danger cart-badge" style="display: none;">0</span>
                                @endauth
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('wishlist.index') }}">
                                <i class="fas fa-heart"></i> Wishlist
                                @auth
                                    @if(Auth::user()->customer && Auth::user()->customer->wishlist && Auth::user()->customer->wishlist->wishlistItems->count() > 0)
                                        <span class="badge bg-danger">{{ Auth::user()->customer->wishlist->wishlistItems->count() }}</span>
                                    @endif
                                @endauth
                            </a>
                        </li>

                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->user_name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    @if(Auth::user()->admin)
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            {{ __('Admin Dashboard') }}
                                        </a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('orders.index') }}">
                                        {{ __('My Orders') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @if (session('success'))
                <div class="container">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="container">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="bg-dark text-white py-4 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h5>ClickToBuy</h5>
                        <p>Your One-Stop Online Shopping Destination</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Quick Links</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('products.index') }}" class="text-white">Products</a></li>
                            <li><a href="{{ route('about') }}" class="text-white">About Us</a></li>
                            <li><a href="{{ route('contact') }}" class="text-white">Contact Us</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Contact</h5>
                        <address class="text-white">
                            123 E-Commerce St.<br>
                            Shopping City, SC 12345<br>
                            <i class="fas fa-envelope"></i> info@clicktobuy.com<br>
                            <i class="fas fa-phone"></i> (123) 456-7890
                        </address>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <p class="mb-0">&copy; {{ date('Y') }} ClickToBuy. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
