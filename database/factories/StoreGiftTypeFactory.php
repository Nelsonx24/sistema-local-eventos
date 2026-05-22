<?php

namespace Database\Factories;

use App\Models\StoreGiftType;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreGiftTypeFactory extends Factory
{
    protected $model = StoreGiftType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }
}
