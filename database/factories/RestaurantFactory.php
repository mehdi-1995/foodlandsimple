<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    public function definition()
    {
        return [
            'vendor_id' => User::factory()->create(['role' => 'vendor'])->id,
            'name' => $this->faker->company() . ' ' . $this->faker->farsiCategory(),
            'type' => $this->faker->randomElement(['restaurant', 'cafe', 'bakery', 'supermarket']),
            'category' => $this->faker->farsiCategory(),
            'rating' => $this->faker->randomFloat(1, 3, 5),
            'reviews_count' => $this->faker->numberBetween(0, 200),
            'delivery_cost' => $this->faker->numberBetween(5000, 20000),
            'delivery_time' => $this->faker->randomElement(['20-30 دقیقه', '30-45 دقیقه', '15-25 دقیقه']),
            'image' => 'https://source.unsplash.com/600x200/?restaurant' . urlencode($this->faker->farsiName()),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
