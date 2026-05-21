<?php

namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'client_name' => fake()->name(),
            'client_id' => fake()->unique()->numerify('########'),
            'client_phone' => fake()->phoneNumber(),
            'event_type' => fake()->randomElement(['Boda', 'Corporativo', 'Social', 'Cumpleaños', '15 Años']),
            'date' => fake()->dateTimeBetween('now', '+6 months'),
            'total_amount' => fake()->randomFloat(2, 1000, 10000),
            'advance_payment' => fake()->randomFloat(2, 100, 5000),
            'balance_pending' => fn (array $attrs) => $attrs['total_amount'] - $attrs['advance_payment'],
            'payment_due_date' => fn (array $attrs) => Carbon::parse($attrs['date'])->subDay()->toDateString(),
            'payment_status' => 'pending',
            'event_status' => 'upcoming',
            'registered_by' => 'Sistema',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_status' => 'completed',
            'payment_status' => 'paid',
        ]);
    }
}
