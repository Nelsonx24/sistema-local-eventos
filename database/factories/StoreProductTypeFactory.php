<?php

namespace Database\Factories;

use App\Models\StoreProductType;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreProductTypeFactory extends Factory
{
    protected $model = StoreProductType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }
}
