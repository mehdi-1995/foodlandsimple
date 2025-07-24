<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\CourierDashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// مسیرهای عمومی (بدون نیاز به احراز هویت)
Route::post('/login', [AuthController::class, 'login']);
Route::get('/restaurants', [RestaurantController::class, 'index']);
Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
Route::get('/restaurants/{id}/menu', [RestaurantController::class, 'menu']);

// مسیرهای محافظت‌شده
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/restaurants/{id}/reviews', [RestaurantController::class, 'storeReview']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::get('/cart/count', [CartController::class, 'count']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'remove']);
    Route::get('/vendor/orders', [VendorDashboardController::class, 'orders']);
    Route::put('/orders/{id}/status', [VendorDashboardController::class, 'updateOrderStatus']);
    Route::post('/vendor/menu', [VendorDashboardController::class, 'storeMenuItem']);
    Route::delete('/menu-items/{id}', [VendorDashboardController::class, 'deleteMenuItem']);
    Route::get('/courier/orders', [CourierDashboardController::class, 'orders']);
    Route::put('/orders/{id}/status', [CourierDashboardController::class, 'updateOrderStatus']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
