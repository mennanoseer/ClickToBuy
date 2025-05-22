<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Wishlist;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $adminUser = User::create([
            'user_name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'address' => '123 Admin St',
            'phone_number' => '123-456-7890',
        ]);

        Admin::create([
            'admin_id' => $adminUser->user_id,
            'role' => 'super_admin',
            'last_login' => now(),
        ]);

        // Create Customer User
        $customerUser = User::create([
            'user_name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'address' => '456 Customer Ave',
            'phone_number' => '098-765-4321',
        ]);

        $customer = Customer::create([
            'customer_id' => $customerUser->user_id,
            'registration_date' => now(),
            'loyalty_points' => 100,
        ]);

        // Create cart and wishlist for the customer
        Cart::create([
            'customer_id' => $customer->customer_id,
            'created_date' => now(),
        ]);

        Wishlist::create([
            'customer_id' => $customer->customer_id,
            'created_date' => now(),
        ]);

        // Create Categories
        $electronics = Category::create(['name' => 'Electronics']);
        $clothing = Category::create(['name' => 'Clothing']);
        $books = Category::create(['name' => 'Books']);
        
        // Create subcategories
        Category::create(['name' => 'Smartphones', 'parent_category_id' => $electronics->category_id]);
        Category::create(['name' => 'Laptops', 'parent_category_id' => $electronics->category_id]);
        Category::create(['name' => 'Men\'s Clothing', 'parent_category_id' => $clothing->category_id]);
        Category::create(['name' => 'Women\'s Clothing', 'parent_category_id' => $clothing->category_id]);
        Category::create(['name' => 'Fiction', 'parent_category_id' => $books->category_id]);
        Category::create(['name' => 'Non-Fiction', 'parent_category_id' => $books->category_id]);
        
        // Create example products
        for ($i = 1; $i <= 20; $i++) {
            Product::create([
                'name' => "Product $i",
                'description' => "This is the description for product $i. It contains detailed information about the product's features and benefits.",
                'price' => rand(10, 1000) / 10,
                'stock' => rand(5, 100),
                'is_active' => true,
                'category_id' => rand(1, 6),
            ]);
        }
        
        // Run additional seeders
        $this->call([
            NotificationSeeder::class,
        ]);
    }
}
