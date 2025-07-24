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
            return $q->where('category', $type); // تغییر 'type' به 'category' برای سازگاری با مدل
        })->paginate(9);
        return view('restaurants.index', compact('restaurants'));
    }

    public function show($id)
    {
        $restaurant = Restaurant::with(['menuItems' => function ($query) {
            $query->paginate(9);
        }])->findOrFail($id);
        return view('restaurants.show', compact('restaurant'));
    }

    public function menu($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $menuItems = $restaurant->menuItems()->paginate(9);
        return view('restaurants.menu', compact('restaurant', 'menuItems'));
    }

    public function storeReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        \App\Models\Review::create([
            'restaurant_id' => $id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('restaurants.show', $id)->with('success', 'نظر شما ثبت شد!');
    }
}
