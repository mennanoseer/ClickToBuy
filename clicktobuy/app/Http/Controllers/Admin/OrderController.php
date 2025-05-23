<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer.user']);
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range with defaults to avoid errors
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }
        
        // Search by order ID or customer name
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('order_id', 'like', "%{$searchTerm}%")
                  ->orWhereHas('customer.user', function($query) use ($searchTerm) {
                      $query->where('user_name', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        $orders = $query->orderBy('order_date', 'desc')->paginate(15);
        
        // If AJAX request, return only the table content
        if ($request->ajax() || $request->has('ajax')) {
            return view('admin.orders.partials.orders_table', compact('orders'))->render();
        }
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::with([
            'customer.user',
            'orderItems.product',
            'payment',
            'shipment',
            'status_history.user'
        ])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of the specified order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'note' => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);
        
        // Only update if status has changed
        if ($order->status !== $request->status) {
            $oldStatus = $order->status;
            $order->status = $request->status;
            $order->save();
            
            // Create status history entry
            \App\Models\OrderStatusHistory::create([
                'order_id' => $order->order_id,
                'status' => $request->status,
                'previous_status' => $oldStatus,
                'note' => $request->note,
                'updated_by' => auth()->user()->user_id
            ]);
        }

        // If order is cancelled, restore product stock
        if ($request->status == 'cancelled') {
            foreach ($order->orderItems as $item) {
                $product = $item->product;
                $product->stock += $item->quantity;
                $product->save();
            }
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully!',
                'order' => [
                    'id' => $order->order_id,
                    'status' => $order->status,
                    'statusClass' => $this->getStatusClass($order->status)
                ]
            ]);
        }

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Order status updated successfully!');
    }

    /**
     * Update the shipment information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateShipment(Request $request, $id)
    {
        $request->validate([
            'carrier' => 'required|string|max:50',
            'tracking_number' => 'required|string|max:100',
            'status' => 'required|string|max:50',
        ]);

        $order = Order::findOrFail($id);
        $shipment = $order->shipment;
        
        if (!$shipment) {
            return redirect()->back()->with('error', 'Shipment record not found.');
        }
        
        $shipment->carrier = $request->carrier;
        $shipment->tracking_number = $request->tracking_number;
        $shipment->status = $request->status;
        
        if ($shipment->status == 'shipped' && !$shipment->shipment_date) {
            $shipment->shipment_date = now();
        }
        
        $shipment->save();
        
        // Update order status if shipment status is updated
        if ($shipment->status == 'shipped' && $order->status == 'processing') {
            $order->status = 'shipped';
            $order->save();
        } elseif ($shipment->status == 'delivered' && $order->status == 'shipped') {
            $order->status = 'delivered';
            $order->save();
        }

        return redirect()->route('admin.orders.show', $id)
            ->with('success', 'Shipment details updated successfully!');
    }

    /**
     * Get the Bootstrap class for status badges
     *
     * @param string $status
     * @return string
     */
    private function getStatusClass($status)
    {
        switch ($status) {
            case 'pending':
                return 'bg-warning';
            case 'processing':
                return 'bg-info';
            case 'shipped':
                return 'bg-primary';
            case 'delivered':
                return 'bg-success';
            case 'cancelled':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }
}
