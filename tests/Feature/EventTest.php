<?php

use App\Models\Config;
use App\Models\Event;
use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('lists events', function () {
    Event::factory(3)->create();

    $this->get(route('events.index'))
        ->assertSuccessful()
        ->assertSee('Eventos');
});

it('can create an event', function () {
    $eventData = [
        'client_name' => 'Juan Pérez',
        'client_id' => '12345678',
        'client_phone' => '71234567',
        'event_type' => 'Boda',
        'date' => now()->addMonth()->format('Y-m-d'),
        'total_amount' => 5000,
        'advance_payment' => 1000,
    ];

    $this->post(route('events.store'), $eventData)
        ->assertRedirect(route('events.index'));

    $this->assertDatabaseHas('events', [
        'client_name' => 'Juan Pérez',
        'event_type' => 'Boda',
    ]);
});

it('requires client_name to create event', function () {
    $this->post(route('events.store'), [
        'event_type' => 'Boda',
        'date' => now()->addMonth()->format('Y-m-d'),
    ])->assertSessionHasErrors('client_name');
});

it('can view a single event', function () {
    $event = Event::factory()->create();

    $this->get(route('events.show', $event))
        ->assertSuccessful()
        ->assertSee($event->client_name);
});

it('can update an event', function () {
    $event = Event::factory()->create();

    $this->put(route('events.update', $event), [
        'client_name' => 'Maria Updated',
        'client_phone' => '79876543',
        'event_type' => 'Social',
        'date' => now()->addMonths(2)->format('Y-m-d'),
        'total_amount' => 8000,
        'advance_payment' => 2000,
    ])->assertRedirect(route('events.index'));

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'event_type' => 'Social',
    ]);
});

it('can close an event', function () {
    $event = Event::factory()->create(['event_status' => 'upcoming']);

    $this->post(route('events.close', $event), [
        'total_amount' => 5000,
        'advance_payment' => 5000,
    ])->assertRedirect();

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'event_status' => 'completed',
    ]);
});

it('can delete an event', function () {
    $event = Event::factory()->create();

    $this->delete(route('events.destroy', $event))
        ->assertRedirect(route('events.index'));

    $this->assertSoftDeleted($event);
});

it('can add an event type', function () {
    Config::where('key', 'event_types')->delete();

    $this->post(route('events.types'), [
        'new_type' => 'Boda',
    ])->assertRedirect();
});
