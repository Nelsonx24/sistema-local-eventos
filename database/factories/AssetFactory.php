<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'category' => fake()->randomElement(['Mobiliario', 'Equipo', 'Vajilla', 'Decoración']),
            'quantity' => fake()->numberBetween(1, 50),
            'condition' => fake()->randomElement(['Bueno', 'Regular', 'Malo']),
            'last_maintenance' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
