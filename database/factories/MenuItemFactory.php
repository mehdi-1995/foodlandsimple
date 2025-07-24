<?php

namespace Database\Factories;

use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuItemFactory extends Factory
{
    public function definition()
    {
        return [
            'restaurant_id' => Restaurant::factory(),
            'name' => $this->faker->farsiFoodName(),
            'price' => $this->faker->numberBetween(20000, 200000),
            'description' => $this->faker->farsiDescription(),
            'category' => $this->faker->randomElement(['appetizer', 'main', 'dessert']),
            'image' => 'https://via.placeholder.com/150x100?text=' . urlencode($this->faker->farsiFoodName()),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
