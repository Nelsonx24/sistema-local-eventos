<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'client_name' => fake()->name(),
            'amount' => fake()->randomFloat(2, 50, 2000),
            'cash_received' => fn (array $attrs) => $attrs['amount'],
            'change_given' => 0,
            'date' => now(),
            'payment_method' => fake()->randomElement(['Efectivo', 'QR', 'Tarjeta']),
            'status' => 'completed',
            'seller_name' => 'Sistema',
        ];
    }
}
