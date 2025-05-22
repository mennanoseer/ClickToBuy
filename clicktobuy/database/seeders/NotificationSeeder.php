<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Notifications\Admin\LowStockNotification;
use App\Notifications\Admin\NewOrderNotification;
use App\Notifications\Admin\NewReviewNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find admin users
        $adminUsers = User::whereHas('admin')->get();
        
        if ($adminUsers->isEmpty()) {
            $this->command->info('No admin users found. Skipping notification seeding.');
            return;
        }
        
        // Get some random orders, products, and reviews
        $orders = Order::inRandomOrder()->take(3)->get();
        $products = Product::where('stock', '<=', 5)->inRandomOrder()->take(2)->get();
        $reviews = Review::where('rating', '<=', 3)->inRandomOrder()->take(2)->get();
        
        foreach ($adminUsers as $admin) {
            // Seed new order notifications
            foreach ($orders as $order) {
                $admin->notify(new NewOrderNotification($order));
                $this->command->info("New order notification created for admin {$admin->user_name}");
            }
            
            // Seed low stock notifications
            foreach ($products as $product) {
                $admin->notify(new LowStockNotification($product));
                $this->command->info("Low stock notification created for admin {$admin->user_name}");
            }
            
            // Seed negative review notifications
            foreach ($reviews as $review) {
                $admin->notify(new NewReviewNotification($review));
                $this->command->info("New review notification created for admin {$admin->user_name}");
            }
        }
    }
}
