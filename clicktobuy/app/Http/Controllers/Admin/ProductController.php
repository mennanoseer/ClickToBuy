<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by stock status
        if ($request->has('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->where('stock', '<', 10);
            } elseif ($request->stock_status === 'out') {
                $query->where('stock', '=', 0);
            }
        }

        // Sort products
        $sortColumn = $request->sort_by ?? 'created_at';
        $sortDirection = $request->sort_dir ?? 'desc';
        $query->orderBy($sortColumn, $sortDirection);

        $products = $query->with('category')->paginate(15);
        $categories = Category::all();
        
        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'is_active' => $request->is_active == 1 ? true : false,
            'category_id' => $request->category_id,
            'image_url' => $imagePath,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::all();
        
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product = Product::findOrFail($id);

        // Handle image upload or removal
        if ($request->hasFile('image')) {
            // Delete old image if exists and it's not an external URL
            if ($product->image_url && !filter_var($product->image_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image_url);
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image_url = $imagePath;
        } elseif ($request->has('remove_image') && $request->remove_image == 1) {
            // If user wants to remove the image
            if ($product->image_url && !filter_var($product->image_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($product->image_url);
            }
            $product->image_url = null;
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->is_active = $request->is_active == 1 ? true : false;
        $product->category_id = $request->category_id;
        $product->save();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete the product image if it's a local file
        if ($product->image_url && !filter_var($product->image_url, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($product->image_url);
        }
        
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Import products from external API
     *
     * @param Request $request
     * @param ExternalProductService $productService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function importExternalProducts(Request $request, \App\Services\ExternalProductService $productService)
    {
        $count = $request->input('count', 10);
        $source = $request->input('source', 'fakestoreapi');
        
        $imported = $productService->importProductsFromAPI($count, $source);
        
        if ($imported > 0) {
            $sourceName = $source === 'dummyjson' ? 'DummyJSON API' : 'Fake Store API';
            return redirect()->route('admin.products.index')
                ->with('success', "{$imported} products were successfully imported from {$sourceName}!");
        }
        
        return redirect()->route('admin.products.index')
            ->with('error', "Failed to import products. Please try again.");
    }
}
