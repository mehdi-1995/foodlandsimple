<?php

namespace App\Http\Controllers\API;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'text' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $request->restaurant_id,
            'text' => $request->text,
            'rating' => $request->rating
        ]);

        return response()->json($review, 201);
    }
}