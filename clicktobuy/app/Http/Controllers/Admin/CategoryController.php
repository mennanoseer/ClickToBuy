<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::with('parentCategory')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'parent_category_id' => 'nullable|exists:categories,category_id',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        try {
            Category::create([
                'name' => $request->name,
                'parent_category_id' => $request->parent_category_id,
                'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
                'description' => $request->description,
                'seo_title' => $request->seo_title,
                'seo_description' => $request->seo_description,
            ]);

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::where('category_id', '!=', $id)->get();
        
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'parent_category_id' => 'nullable|exists:categories,category_id',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        
        // Prevent circular reference
        if ($request->parent_category_id == $id) {
            return redirect()->back()
                ->with('error', 'A category cannot be its own parent.')
                ->withInput();
        }
        
        try {
            $category->name = $request->name;
            $category->parent_category_id = $request->parent_category_id;
            $category->is_active = $request->has('is_active') ? (bool)$request->is_active : true;
            $category->description = $request->description;
            $category->seo_title = $request->seo_title;
            $category->seo_description = $request->seo_description;
            $category->save();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating category: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with associated products.');
        }
        
        // Update child categories to have no parent
        Category::where('parent_category_id', $id)
            ->update(['parent_category_id' => null]);
            
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
