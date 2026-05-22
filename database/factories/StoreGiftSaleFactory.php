<?php

namespace Database\Factories;

use App\Models\StoreGift;
use App\Models\StoreGiftSale;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreGiftSaleFactory extends Factory
{
    protected $model = StoreGiftSale::class;

    public function definition(): array
    {
        return [
            'store_gift_id' => StoreGift::factory(),
            'quantity' => fake()->numberBetween(1, 10),
            'unit_cost' => fake()->randomFloat(2, 5, 30),
            'unit_price' => fn (array $attrs) => $attrs['unit_cost'] * 2,
            'total_amount' => fn (array $attrs) => $attrs['quantity'] * $attrs['unit_price'],
            'profit' => fn (array $attrs) => $attrs['quantity'] * ($attrs['unit_price'] - $attrs['unit_cost']),
            'date' => fake()->dateTimeThisMonth(),
        ];
    }
}
