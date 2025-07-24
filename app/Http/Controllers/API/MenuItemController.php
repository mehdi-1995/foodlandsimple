<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuItem;

class MenuItemController extends Controller
{
    public function index($restaurantId)
    {
        $menuItems = MenuItem::where('restaurant_id', $restaurantId)->get();
        return response()->json($menuItems);
    }

    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string',
            'price' => 'required|integer',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'image' => 'nullable|string'
        ]);

        $menuItem = MenuItem::create($request->all());
        return response()->json($menuItem, 201);
    }

    public function destroy($id)
    {
        MenuItem::findOrFail($id)->delete();
        return response()->json(['message' => 'آیتم منو حذف شد']);
    }
}
