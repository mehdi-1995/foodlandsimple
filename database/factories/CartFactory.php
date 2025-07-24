<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'menu_item_id' => MenuItem::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
        ];
    }
}
