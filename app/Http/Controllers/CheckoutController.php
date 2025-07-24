<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout');
    }

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

        return redirect()->route('profile')->with('success', 'سفارش شما ثبت شد!');
    }
}
