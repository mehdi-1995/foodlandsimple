<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('menuItem')->where('user_id', Auth::id())->get();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'سبد خرید شما خالی است.');
        }
        return view('checkout', compact('cartItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:online,cod',
            'address' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $cartItems = Cart::with('menuItem')->where('user_id', Auth::id())->get();
            if ($cartItems->isEmpty()) {
                throw new \Exception('سبد خرید خالی است.');
            }

            $total = $cartItems->sum(function ($item) {
                return $item->menuItem->price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => Auth::id(),
                'restaurant_id' => $cartItems->first()->menuItem->restaurant_id,
                'courier_id' => \App\Models\User::where('role', 'courier')->inRandomOrder()->first()->id,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'address' => $request->address,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item->menu_item_id,
                    'quantity' => $item->quantity,
                    'price' => $item->menuItem->price,
                ]);
            }

            Cart::where('user_id', Auth::id())->delete();

            DB::commit();
            return redirect()->route('profile')->with('success', 'سفارش شما با موفقیت ثبت شد!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout')->with('error', 'خطا در ثبت سفارش: ' . $e->getMessage());
        }
    }
}
