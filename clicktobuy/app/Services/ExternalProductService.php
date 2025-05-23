<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Category;

class ExternalProductService
{
    /**
     * Import products from an external API
     *
     * @param int $limit Number of products to import
     * @return int Number of products imported
     */
    public function importProductsFromAPI($limit = 10)
    {
        // Fetch products from Fake Store API
        $response = Http::get("https://fakestoreapi.com/products?limit={$limit}");
        
        if ($response->successful()) {
            $products = $response->json();
            
            foreach ($products as $productData) {
                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => $productData['category']],
                    ['name' => $productData['category']]
                );                // Create the product
                Product::updateOrCreate(
                    ['name' => $productData['title']],
                    [
                        'category_id' => $category->category_id,                        'description' => $productData['description'], // Now we can use the full description
                        'price' => $productData['price'],
                        'stock' => rand(0, 50), // Random stock value
                        'image_url' => $productData['image'],
                        'is_active' => true,
                        'units_sold' => 0
                    ]
                );
            }
            
            return count($products);
        }
        
        return 0;
    }
}
