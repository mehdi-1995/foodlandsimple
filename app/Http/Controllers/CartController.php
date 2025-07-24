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
        return response()->json($cartItems->map(function ($item) {
            return [
                'id' => $item->id,
                'menu_item' => $item->menuItem,
                'quantity' => $item->quantity,
            ];
        }));
    }

    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->count();
        return response()->json(['count' => $count]);
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

        return response()->json(['message' => 'آیتم به سبد خرید اضافه شد!'], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['message' => 'سبد خرید به‌روزرسانی شد']);
    }

    public function remove($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'آیتم از سبد خرید حذف شد']);
    }
}
