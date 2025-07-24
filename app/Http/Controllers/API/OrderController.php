<?php

namespace App\Http\Controllers\API;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:online,cod',
            'address' => 'required|string'
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);
            $total += $menuItem->price * $item['quantity'];
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $request->restaurant_id,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'address' => $request->address
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $item['menu_item_id'],
                'quantity' => $item['quantity'],
                'price' => MenuItem::find($item['menu_item_id'])->price
            ]);
        }

        return response()->json($order, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,shipped,delivered'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        return response()->json(['message' => 'وضعیت سفارش به‌روزرسانی شد']);
    }

    public function vendorOrders()
    {
        $vendorId = Auth::user()->id;
        $orders = Order::whereIn('restaurant_id', function ($query) use ($vendorId) {
            $query->select('id')->from('restaurants')->where('vendor_id', $vendorId);
        })->with('orderItems.menuItem')->get();
        return response()->json($orders);
    }

    public function courierOrders()
    {
        $orders = Order::where('courier_id', Auth::id())->whereIn('status', ['pending', 'shipped'])->with('orderItems.menuItem')->get();
        return response()->json($orders);
    }
}
