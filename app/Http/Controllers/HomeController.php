<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q');
        $type = $request->query('type', 'all');
        $restaurants = Restaurant::when($query, function ($q) use ($query) {
            return $q->where('name', 'like', "%$query%")->orWhere('category', 'like', "%$query%");
        })->when($type !== 'all', function ($q) use ($type) {
            return $q->where('type', $type);
        })->paginate(9);
        return view('home', compact('restaurants'));
    }
}
