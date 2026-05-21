<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word().'_620cc',
            'category' => fake()->randomElement(['Cerveza', 'Gaseosa', 'Agua', 'Lata']),
            'boxes' => fake()->numberBetween(5, 50),
            'units_per_box' => 12,
            'loose_units' => fake()->numberBetween(0, 20),
            'price_per_box' => fake()->randomFloat(2, 80, 200),
            'price_per_unit' => fake()->randomFloat(2, 8, 20),
            'status' => 'Active',
        ];
    }
}
