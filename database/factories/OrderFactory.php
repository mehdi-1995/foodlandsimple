<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory()->create(['role' => 'customer'])->id,
            'restaurant_id' => Restaurant::factory(),
            'courier_id' => User::factory()->create(['role' => 'courier'])->id,
            'total' => $this->faker->numberBetween(50000, 500000),
            'payment_method' => $this->faker->randomElement(['online', 'cod']),
            'status' => $this->faker->randomElement(['pending', 'preparing', 'shipped', 'delivered']),
            'address' => $this->faker->farsiAddress(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
