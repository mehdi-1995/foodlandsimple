<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorDashboardController extends Controller
{
    public function index()
    {
        $vendorId = Auth::id();
        $restaurant = Restaurant::where('vendor_id', $vendorId)->first();
        $orders = Order::where('restaurant_id', $restaurant->id)->with('orderItems.menuItem')->get();
        $menuItems = MenuItem::where('restaurant_id', $restaurant->id)->get();
        return view('vendor.dashboard', compact('restaurant', 'orders', 'menuItems'));
    }

    public function storeMenuItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'price' => 'required|integer',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'image' => 'nullable|string'
        ]);

        $vendorId = Auth::id();
        $restaurant = Restaurant::where('vendor_id', $vendorId)->first();

        MenuItem::create([
            'restaurant_id' => $restaurant->id,
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
            'image' => $request->image ?? 'https://via.placeholder.com/150x100'
        ]);

        return redirect()->route('vendor.dashboard')->with('success', 'آیتم منو اضافه شد!');
    }

    public function deleteMenuItem($id)
    {
        MenuItem::findOrFail($id)->delete();
        return redirect()->route('vendor.dashboard')->with('success', 'آیتم منو حذف شد!');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,shipped'
        ]);

        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        return redirect()->route('vendor.dashboard')->with('success', 'وضعیت سفارش به‌روزرسانی شد!');
    }
}
