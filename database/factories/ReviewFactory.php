<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory()->create(['role' => 'customer'])->id,
            'restaurant_id' => Restaurant::factory(),
            'text' => $this->faker->farsiDescription(),
            'rating' => $this->faker->numberBetween(1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
