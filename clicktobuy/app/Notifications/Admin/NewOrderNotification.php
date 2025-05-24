<?php

namespace App\Notifications\Admin;

use App\Models\Order;

class NewOrderNotification extends AdminNotification
{
    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function __construct(Order $order)
    {
        $title = 'New Order Received';
        $message = "Order #{$order->order_id} has been placed by {$order->customer->user->user_name} for \${$order->total_price}.";
        $link = route('admin.orders.show', $order->order_id);
        
        parent::__construct($title, $message, $link, 'fa-shopping-cart', 'bg-success');
    }
}
