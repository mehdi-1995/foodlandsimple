<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\RestaurantController;
use App\Http\Controllers\API\MenuItemController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/restaurants', [RestaurantController::class, 'index']);
    Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
    Route::get('/restaurants/{restaurantId}/menu', [MenuItemController::class, 'index']);
    Route::post('/menu-items', [MenuItemController::class, 'store']);
    Route::delete('/menu-items/{id}', [MenuItemController::class, 'destroy']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::get('/vendor/orders', [OrderController::class, 'vendorOrders']);
    Route::get('/courier/orders', [OrderController::class, 'courierOrders']);
    Route::post('/reviews', [ReviewController::class, 'store']);
});
Route::post('/login', [AuthController::class, 'login']);