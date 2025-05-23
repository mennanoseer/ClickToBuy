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
            if ($orders->isNotEmpty()) {
                foreach ($orders as $order) {
                    $admin->notify(new NewOrderNotification($order));
                    $this->command->info("New order notification created for admin {$admin->user_name}");
                }
            } else {
                $this->command->info("No orders found. Skipping order notifications.");
            }
            
            // Seed low stock notifications
            if ($products->isNotEmpty()) {
                foreach ($products as $product) {
                    $admin->notify(new LowStockNotification($product));
                    $this->command->info("Low stock notification created for admin {$admin->user_name}");
                }
            } else {
                $this->command->info("No low stock products found. Skipping low stock notifications.");
            }
            
            // Seed negative review notifications
            if ($reviews->isNotEmpty()) {
                foreach ($reviews as $review) {
                    $admin->notify(new NewReviewNotification($review));
                    $this->command->info("New review notification created for admin {$admin->user_name}");
                }
            } else {
                $this->command->info("No negative reviews found. Skipping review notifications.");
            }
        }
    }
}
