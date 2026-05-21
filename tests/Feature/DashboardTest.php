<?php

use App\Models\Event;
use App\Models\Inventory;
use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('loads the dashboard page', function () {
    Event::factory(3)->completed()->create();
    Inventory::factory(2)->create();

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Dashboard')
        ->assertSee('Eventos este Mes')
        ->assertSee('Alertas Inventario');
});

it('shows inventory alerts when stock is low', function () {
    Inventory::factory()->create(['boxes' => 1]);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Bajo stock');
});

it('shows chart data for completed events', function () {
    Event::factory(4)->completed()->create(['event_type' => 'Boda']);

    $this->get(route('dashboard'))
        ->assertSuccessful()
        ->assertSee('Ventas de Paceña')
        ->assertSee('Ventas Huari');
});
