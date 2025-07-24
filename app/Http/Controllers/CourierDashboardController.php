<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourierDashboardController extends Controller
{
    public function index()
    {
        $orders = Order::where('courier_id', Auth::id())->whereIn('status', ['pending', 'shipped'])->with('orderItems.menuItem')->get();
        return view('courier.dashboard', compact('orders'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:shipped,delivered'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        return redirect()->route('courier.dashboard')->with('success', 'وضعیت سفارش به‌روزرسانی شد!');
    }
}
