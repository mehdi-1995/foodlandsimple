<?php

namespace App\Http\Controllers\API;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q');
        $type = $request->query('type');
        $restaurants = Restaurant::when($query, function ($q) use ($query) {
            return $q->where('name', 'like', "%$query%")->orWhere('category', 'like', "%$query%");
        })->when($type, function ($q) use ($type) {
            return $type === 'all' ? $q : $q->where('type', $type);
        })->get();
        return response()->json($restaurants);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with('menuItems', 'reviews')->findOrFail($id);
        return response()->json($restaurant);
    }
}
