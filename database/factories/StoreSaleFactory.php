<?php

namespace Database\Factories;

use App\Models\StoreProduct;
use App\Models\StoreSale;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreSaleFactory extends Factory
{
    protected $model = StoreSale::class;

    public function definition(): array
    {
        return [
            'store_product_id' => StoreProduct::factory(),
            'quantity' => fake()->numberBetween(1, 10),
            'unit_cost' => fake()->randomFloat(2, 10, 50),
            'unit_price' => fn (array $attrs) => $attrs['unit_cost'] * 2,
            'total_amount' => fn (array $attrs) => $attrs['quantity'] * $attrs['unit_price'],
            'profit' => fn (array $attrs) => $attrs['quantity'] * ($attrs['unit_price'] - $attrs['unit_cost']),
            'date' => fake()->dateTimeThisMonth(),
        ];
    }
}
