<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'name' => fake()->name(),
            'role' => 'Vendedor',
            'username' => fake()->unique()->userName(),
            'password' => bcrypt('password'),
            'email' => fake()->unique()->safeEmail(),
            'status' => 'Active',
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'Administrador',
        ]);
    }
}
