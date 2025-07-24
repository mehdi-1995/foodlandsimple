<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q');
        $type = $request->query('type', 'all');
        $restaurants = Restaurant::when($query, function ($q) use ($query) {
            return $q->where('name', 'like', "%$query%")->orWhere('category', 'like', "%$query%");
        })->when($type !== 'all', function ($q) use ($type) {
            return $q->where('type', $type);
        })->get(9);
        return response()->json($restaurants);
    }

    public function show($id)
    {
        $restaurant = Restaurant::with('menuItems', 'reviews')->findOrFail($id);
        return view('restaurants.show', compact('restaurant'));
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'text' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $id,
            'text' => $request->text,
            'rating' => $request->rating
        ]);

        return redirect()->route('restaurants.show', $id)->with('success', 'نظر شما ثبت شد!');
    }
}
