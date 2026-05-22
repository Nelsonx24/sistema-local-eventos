<?php

namespace Database\Factories;

use App\Models\StoreGift;
use App\Models\StoreGiftType;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreGiftFactory extends Factory
{
    protected $model = StoreGift::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'detail' => fake()->sentence(),
            'cost' => fake()->randomFloat(2, 5, 30),
            'sale_price' => fn (array $attrs) => $attrs['cost'] * 2,
            'stock' => fake()->numberBetween(5, 50),
            'barcode' => fake()->unique()->ean13(),
            'category' => fake()->randomElement(['Juguetes', 'Decoración', 'Accesorios']),
            'gift_type_id' => StoreGiftType::factory(),
        ];
    }
}
