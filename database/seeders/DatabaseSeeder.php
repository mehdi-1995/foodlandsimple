<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Cart;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::factory()->create([
            'name' => 'کاربر نمونه',
            'phone' => '09123456789',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'address' => 'تهران، خیابان نمونه'
        ]);

        User::factory()->create([
            'name' => 'فروشنده نمونه',
            'phone' => '09123456790',
            'password' => bcrypt('password'),
            'role' => 'vendor',
            'address' => 'تهران، خیابان نمونه'
        ]);

        User::factory()->create([
            'name' => 'پیک نمونه',
            'phone' => '09123456791',
            'password' => bcrypt('password'),
            'role' => 'courier',
            'address' => 'تهران، خیابان نمونه'
        ]);

        User::factory()->count(10)->create(['role' => 'customer']);
        User::factory()->count(5)->create(['role' => 'vendor']);
        User::factory()->count(5)->create(['role' => 'courier']);

        Restaurant::factory()->count(10)->create()->each(function ($restaurant) {
            MenuItem::factory()->count(5)->create(['restaurant_id' => $restaurant->id]);
            Review::factory()->count(3)->create(['restaurant_id' => $restaurant->id]);
            Cart::factory()->count(2)->create(['user_id' => User::where('role', 'customer')->inRandomOrder()->first()->id]);
        });

        Order::factory()->count(20)->create()->each(function ($order) {
            OrderItem::factory()->count(3)->create(['order_id' => $order->id]);
        });
    }
}
