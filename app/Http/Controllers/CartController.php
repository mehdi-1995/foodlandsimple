<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('menuItem')->where('user_id', Auth::id())->get();
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->menuItem->price * $item->quantity;
        });
        return view('cart', compact('cartItems', 'totalPrice'));
    }

    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->count();
        return view('cart.count', compact('count'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $userId = Auth::id();
        $menuItemId = $request->input('menu_item_id');
        $quantity = $request->input('quantity');

        $cartItem = Cart::where('user_id', $userId)
            ->where('menu_item_id', $menuItemId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'menu_item_id' => $menuItemId,
                'quantity' => $quantity
            ]);
        }

        return back()->with('success', 'آیتم به سبد خرید اضافه شد!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return redirect()->route('cart')->with('success', 'سبد خرید به‌روزرسانی شد');
    }

    public function destroy($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        return redirect()->route('cart')->with('success', 'آیتم از سبد خرید حذف شد');
    }
}
