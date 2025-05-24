<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Remove auth middleware from index to allow guests to see the home page
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $featuredProducts = Product::where('is_active', true)
                                  ->orderBy('created_at', 'desc')
                                  ->take(8)
                                  ->get();
                                  
        $categories = Category::whereNull('parent_category_id')
                             ->with('subcategories')
                             ->take(6)
                             ->get();
                             
        return view('home', compact('featuredProducts', 'categories'));
    }
    
    /**
     * Show the about page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about()
    {
        return view('about');
    }
    
    /**
     * Show the contact page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function contact()
    {
        return view('contact');
    }
    
    /**
     * Handle contact form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContact(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'subject' => 'required|string|max:100',
            'message' => 'required|string',
        ]);
        
        // In a real application, you would send an email here
        // For demo purposes, we'll just return with success message
        // Mail::to('info@clicktobuy.com')->send(new ContactFormMail($validatedData));
        
        return redirect()->back()->with('success', 'Thank you for your message. We will get back to you shortly!');
    }
}
