<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Notifications\Admin\LowStockNotification;
use App\Notifications\Admin\NewOrderNotification;
use App\Notifications\Admin\NewReviewNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for new orders
        Order::created(function ($order) {
            $this->notifyAdmins(new NewOrderNotification($order));
        });
        
        // Listen for low stock
        Product::updated(function ($product) {
            if ($product->isDirty('stock') && $product->stock <= 5 && $product->getOriginal('stock') > 5) {
                $this->notifyAdmins(new LowStockNotification($product));
            }
        });
        
        // Listen for new reviews, particularly negative ones
        Review::created(function ($review) {
            // Always notify for negative reviews (3 stars or less)
            // For positive reviews (4-5 stars), notify randomly (about 1 in 3)
            if ($review->rating <= 3 || rand(1, 3) == 1) {
                $this->notifyAdmins(new NewReviewNotification($review));
            }
        });
    }
    
    /**
     * Notify all admin users.
     *
     * @param mixed $notification
     * @return void
     */
    protected function notifyAdmins($notification)
    {
        // Find all users that are admins
        $adminUsers = User::whereHas('admin')->get();
        
        foreach ($adminUsers as $admin) {
            $admin->notify($notification);
        }
    }
}
