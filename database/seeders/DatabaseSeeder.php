<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Restaurant;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'فروشنده نمونه',
            'email' => 'vendor@example.com',
            'phone' => '09123456789',
            'password' => bcrypt('password'),
            'role' => 'vendor'
        ]);

        Restaurant::create([
            'vendor_id' => 1,
            'name' => 'رستوران نمونه',
            'type' => 'restaurant',
            'category' => 'ایرانی، فست‌فود',
            'rating' => 4.5,
            'reviews_count' => 120,
            'delivery_cost' => 10000,
            'delivery_time' => '30-45 دقیقه',
            'image' => 'https://via.placeholder.com/600x200'
        ]);
    }
}
