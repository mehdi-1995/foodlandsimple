<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\VendorDashboardController;
use App\Http\Controllers\CourierDashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');
Route::post('/restaurants/{id}/reviews', [RestaurantController::class, 'storeReview'])->name('restaurants.reviews.store');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile')->middleware('auth');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout')->middleware('auth');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store')->middleware('auth');
Route::get('/vendor/dashboard', [VendorDashboardController::class, 'index'])->name('vendor.dashboard')->middleware(['auth', 'role:vendor']);
Route::post('/vendor/menu', [VendorDashboardController::class, 'storeMenuItem'])->name('vendor.menu.store')->middleware(['auth', 'role:vendor']);
Route::delete('/vendor/menu/{id}', [VendorDashboardController::class, 'deleteMenuItem'])->name('vendor.menu.delete')->middleware(['auth', 'role:vendor']);
Route::put('/vendor/orders/{id}/status', [VendorDashboardController::class, 'updateOrderStatus'])->name('vendor.orders.status')->middleware(['auth', 'role:vendor']);
Route::get('/courier/dashboard', [CourierDashboardController::class, 'index'])->name('courier.dashboard')->middleware(['auth', 'role:courier']);
Route::put('/courier/orders/{id}/status', [CourierDashboardController::class, 'updateOrderStatus'])->name('courier.orders.status')->middleware(['auth', 'role:courier']);

Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::post('/contact', function () {
    // منطق ارسال پیام (می‌تونید از ایمیل یا ذخیره در دیتابیس استفاده کنید)
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
