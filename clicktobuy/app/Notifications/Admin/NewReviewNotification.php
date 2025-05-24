<?php

namespace App\Notifications\Admin;

use App\Models\Review;

class NewReviewNotification extends AdminNotification
{
    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Review  $review
     * @return void
     */
    public function __construct(Review $review)
    {
        $title = $review->rating <= 3 ? 'Negative Review Alert' : 'New Product Review';
        $message = "{$review->customer->user->user_name} left a {$review->rating}-star review for '{$review->product->name}'.";
        $link = route('admin.reviews.show', $review->review_id);
        $iconClass = $review->rating <= 3 ? 'bg-danger' : 'bg-info';
        
        parent::__construct($title, $message, $link, 'fa-star', $iconClass);
    }
}
