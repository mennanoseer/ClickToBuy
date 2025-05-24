<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get all notifications for the authenticated admin
        $query = Auth::user()->notifications();
        
        // Filter by status if provided
        if ($request->has('status')) {
            if ($request->status === 'read') {
                $query->whereNotNull('read_at');
            } elseif ($request->status === 'unread') {
                $query->whereNull('read_at');
            }
        }
        
        // Order by created_at in descending order (newest first)
        $query->orderBy('created_at', 'desc');
        
        $notifications = $query->paginate(15);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        Auth::user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'All notifications marked as read');
    }    /**
     * Get unread notifications count via AJAX.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications->count();
        
        return response()->json(['count' => $count]);
    }
    
    /**
     * Display the specified notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Mark notification as read
        if ($notification && $notification->read_at === null) {
            $notification->markAsRead();
        }
        
        // Redirect based on notification type
        if (isset($notification->data['link'])) {
            return redirect($notification->data['link']);
        }
        
        // If no specific link, redirect back to notifications list
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification marked as read');
    }
}
