<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share categories with all views
        View::composer('*', function ($view) {
            $view->with('globalCategories', Category::whereNull('parent_category_id')->with('childCategories')->get());
        });

        // Share cart count with all views
        View::composer('*', function ($view) {
            $cartCount = 0;
            if (Auth::check() && Auth::user()->customer && Auth::user()->customer->cart) {
                $cartCount = Auth::user()->customer->cart->cartItems->sum('quantity');
            }
            $view->with('cartCount', $cartCount);
        });
        
        // Make sure CSRF token is included with AJAX requests
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        
        // Use custom Bootstrap 4 pagination template
        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
