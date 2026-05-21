<?php

use App\Models\Event;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('lists sales', function () {
    $event = Event::factory()->completed()->create();
    Sale::factory(3)->create(['event_id' => $event->id]);

    $this->get(route('sales.index'))
        ->assertSuccessful();
});

it('shows sales for an event', function () {
    $event = Event::factory()->completed()->create();
    Sale::factory(2)->create(['event_id' => $event->id]);

    $this->get(route('sales.show', $event))
        ->assertSuccessful()
        ->assertSee($event->client_name);
});

it('can process a sale', function () {
    $event = Event::factory()->completed()->create();
    $product = Inventory::factory()->create(['boxes' => 10, 'loose_units' => 10]);

    $this->post(route('sales.process'), [
        'event_id' => (string) $event->id,
        'client_name' => 'Test Client',
        'payment_method' => 'Efectivo',
        'items' => json_encode([
            ['name' => $product->name, 'quantity' => 2, 'type' => 'Caja', 'subtotal' => $product->price_per_box * 2],
        ]),
        'cash_received' => 500,
    ])->assertRedirect();

    $this->assertDatabaseHas('sales', [
        'event_id' => (string) $event->id,
        'client_name' => 'Test Client',
    ]);
});

it('fails sale with insufficient stock', function () {
    $event = Event::factory()->completed()->create();
    $product = Inventory::factory()->create(['boxes' => 1, 'loose_units' => 0]);

    $this->post(route('sales.process'), [
        'event_id' => (string) $event->id,
        'client_name' => 'Test Client',
        'payment_method' => 'Efectivo',
        'items' => json_encode([
            ['name' => $product->name, 'quantity' => 10, 'type' => 'Caja', 'subtotal' => $product->price_per_box * 10],
        ]),
        'cash_received' => 5000,
    ])->assertRedirect();

    $this->assertDatabaseMissing('sales', [
        'event_id' => (string) $event->id,
        'client_name' => 'Test Client',
    ]);
});

it('can delete a sale', function () {
    $event = Event::factory()->completed()->create();
    $sale = Sale::factory()->create(['event_id' => $event->id]);

    $this->delete(route('sales.destroy', $sale))
        ->assertRedirect();

    $this->assertSoftDeleted($sale);
});
