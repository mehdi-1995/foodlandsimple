<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourierDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\VendorDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::post('/restaurants/{id}/reviews', [RestaurantController::class, 'storeReview'])->name('restaurants.reviews.store')->middleware('auth');
Route::get('/restaurants/{id}/menu', [RestaurantController::class, 'menu'])->name('restaurants.menu');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/items', [CartController::class, 'items'])->name('cart.items');
Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile')->middleware('auth');
Route::get('/orders', [OrderController::class, 'index'])->name('orders')->middleware('auth');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout')->middleware('auth');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('auth');
Route::get('/vendor/dashboard', [VendorDashboardController::class, 'index'])->name('vendor.dashboard')->middleware(['auth', 'role:vendor']);
Route::get('/vendor/orders', [VendorDashboardController::class, 'orders'])->name('vendor.orders')->middleware(['auth', 'role:vendor']);
Route::put('/vendor/orders/{id}/status', [VendorDashboardController::class, 'updateOrderStatus'])->name('vendor.orders.status')->middleware(['auth', 'role:vendor']);
Route::post('/vendor/menu', [VendorDashboardController::class, 'storeMenuItem'])->name('vendor.menu.store')->middleware(['auth', 'role:vendor']);
Route::delete('/vendor/menu/{id}', [VendorDashboardController::class, 'deleteMenuItem'])->name('vendor.menu.delete')->middleware(['auth', 'role:vendor']);
Route::get('/courier/dashboard', [CourierDashboardController::class, 'index'])->name('courier.dashboard')->middleware(['auth', 'role:courier']);
Route::get('/courier/orders', [CourierDashboardController::class, 'orders'])->name('courier.orders')->middleware(['auth', 'role:courier']);
Route::put('/courier/orders/{id}/status', [CourierDashboardController::class, 'updateOrderStatus'])->name('courier.orders.status')->middleware(['auth', 'role:courier']);
Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::post('/contact', function () {
    return redirect()->route('contact')->with('success', 'پیام شما با موفقیت ارسال شد!');
})->name('contact.submit');
Route::get('/faq', function () {
    return view('faq');
})->name('faq');
Route::get('/terms', function () {
    return view('terms');
})->name('terms');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/user', function () {
    return view('profile', ['user' => auth()->user()]);
})->middleware('auth')->name('user');
