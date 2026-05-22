<?php

namespace Database\Factories;

use App\Models\StoreProduct;
use App\Models\StoreProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreProductFactory extends Factory
{
    protected $model = StoreProduct::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'detail' => fake()->sentence(),
            'cost' => fake()->randomFloat(2, 10, 50),
            'sale_price' => fn (array $attrs) => $attrs['cost'] * 2,
            'stock' => fake()->numberBetween(5, 50),
            'barcode' => fake()->unique()->ean13(),
            'category' => fake()->randomElement(['Bebidas', 'Snacks', 'Pasteles']),
            'product_type_id' => StoreProductType::factory(),
        ];
    }
}
