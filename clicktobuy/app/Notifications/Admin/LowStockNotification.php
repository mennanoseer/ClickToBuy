<?php

namespace App\Notifications\Admin;

use App\Models\Product;

class LowStockNotification extends AdminNotification
{
    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function __construct(Product $product)
    {
        $title = 'Low Stock Alert';
        $message = "Product '{$product->name}' is running low on stock ({$product->stock} remaining).";
        $link = route('admin.products.edit', $product->product_id);
        
        parent::__construct($title, $message, $link, 'fa-exclamation-triangle', 'bg-warning');
    }
}
