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
     * @param string $source API source to use
     * @return int Number of products imported
     */
    public function importProductsFromAPI($limit = 10, $source = 'fakestoreapi')
    {
        switch ($source) {
            case 'dummyjson':
                return $this->importFromDummyJSON($limit);
            case 'fakestoreapi':
            default:
                return $this->importFromFakeStore($limit);
        }
    }
    
    /**
     * Import products from DummyJSON API (has 100 products)
     * 
     * @param int $limit Number of products to import
     * @return int Number of products imported
     */
    private function importFromDummyJSON($limit)
    {
        // Fetch products from DummyJSON API
        $response = Http::withOptions(['verify' => false])
                     ->get("https://dummyjson.com/products?limit={$limit}");
        
        if ($response->successful()) {
            $data = $response->json();
            $products = $data['products']; // DummyJSON returns products in a nested array
            
            foreach ($products as $productData) {
                // Find or create category
                $category = Category::firstOrCreate(
                    ['name' => $productData['category']],
                    ['name' => $productData['category']]
                );
                
                // Create the product (use the first image from the images array)
                Product::updateOrCreate(
                    ['name' => $productData['title']],
                    [
                        'category_id' => $category->category_id,
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'stock' => $productData['stock'] ?? rand(0, 50),
                        'image_url' => $productData['images'][0] ?? $productData['thumbnail'],
                        'is_active' => true,
                        'units_sold' => 0
                    ]
                );
            }
            
            return count($products);
        }
        
        return 0;
    }
    
    /**
     * Import products from Fake Store API (limited to 20 products)
     * 
     * @param int $limit Number of products to import
     * @return int Number of products imported
     */
    private function importFromFakeStore($limit)
    {
        // Fetch products from Fake Store API with SSL verification disabled
        $response = Http::withOptions(['verify' => false])
                     ->get("https://fakestoreapi.com/products?limit={$limit}");
        
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
                        'category_id' => $category->category_id,'description' => $productData['description'], // Now we can use the full description
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
