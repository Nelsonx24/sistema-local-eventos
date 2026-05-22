<?php

use App\Models\Config;
use App\Models\Event;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

// --- EventController gaps ---

it('returns event edit data as json', function () {
    $event = Event::factory()->create();

    $this->get(route('events.edit-data', $event))
        ->assertSuccessful()
        ->assertJson([
            'client_name' => $event->client_name,
            'event_type' => $event->event_type,
        ]);
});

it('downloads calendar pdf', function () {
    Event::factory(2)->create();

    $this->get(route('events.calendar-pdf'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

it('downloads event report pdf', function () {
    Event::factory(2)->create();

    $this->get(route('events.report-pdf'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

it('downloads event report pdf with year filter', function () {
    Event::factory(2)->create();

    $this->get(route('events.report-pdf', ['year' => now()->year]))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

it('can pay full balance of event', function () {
    $event = Event::factory()->create(['total_amount' => 5000, 'advance_payment' => 1000]);

    $this->post(route('events.pay-balance', $event))
        ->assertRedirect();

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'advance_payment' => 5000,
        'balance_pending' => 0,
        'payment_status' => 'paid',
    ]);
});

it('can upload contract', function () {
    $event = Event::factory()->create();

    $this->post(route('events.upload-contract', $event))
        ->assertRedirect();

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'signed_contract_url' => 'simulated-storage/contract.pdf',
    ]);
});

it('can delete event type', function () {
    $this->post('/events/types', ['new_type' => 'TestType']);
    $this->post('/events/types', ['new_type' => 'AnotherType']);

    expect(Config::getEventTypes())->toContain('TestType');

    $this->delete('/events/types', ['type' => 'TestType'])
        ->assertRedirect();

    expect(Config::getEventTypes())->not->toContain('TestType');
    expect(Config::getEventTypes())->toContain('AnotherType');
});

it('downloads contract pdf', function () {
    $event = Event::factory()->create();

    $this->get(route('events.download-contract', $event))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

// --- SaleController gaps ---

it('loads direct sale page', function () {
    $this->get(route('sales.direct'))
        ->assertSuccessful()
        ->assertSee('Venta Directa');
});

it('can mark sale ticket as printed', function () {
    $event = Event::factory()->completed()->create();
    $sale = Sale::factory()->create(['event_id' => $event->id, 'is_printed' => false]);

    $this->post(route('sales.print', $sale))
        ->assertRedirect();

    $this->assertDatabaseHas('sales', [
        'id' => $sale->id,
        'is_printed' => true,
    ]);
});

it('can close event from sales', function () {
    $event = Event::factory()->create(['status' => 'Pendiente']);

    $this->post(route('sales.event.close', $event))
        ->assertRedirect();

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'status' => 'Cerrado',
    ]);
});

// --- InventoryController gaps ---

it('can audit inventory', function () {
    $item = Inventory::factory()->create(['boxes' => 10, 'loose_units' => 5]);

    $this->post(route('inventory.audit'), [
        'inventory_id' => $item->id,
        'physical_boxes' => 8,
        'physical_loose' => 3,
    ])->assertRedirect();

    $this->assertDatabaseHas('inventory', [
        'id' => $item->id,
        'boxes' => 8,
        'loose_units' => 3,
    ]);
});

it('can update inventory prices', function () {
    $item = Inventory::factory()->create(['price_per_box' => 100, 'price_per_unit' => 10]);

    $this->post(route('inventory.prices'), [
        'inventory_id' => $item->id,
        'price_per_box' => 150,
        'price_per_unit' => 15,
    ])->assertRedirect();

    $this->assertDatabaseHas('inventory', [
        'id' => $item->id,
        'price_per_box' => 150,
        'price_per_unit' => 15,
    ]);
});

it('downloads inventory pdf', function () {
    Inventory::factory(2)->create();

    $this->get(route('inventory.pdf'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

// --- ReportController gaps ---

it('loads direct sales page for a date', function () {
    $date = now()->format('Y-m-d');
    Sale::factory()->create([
        'is_direct_sale' => true,
        'event_id' => 'Venta Directa 1',
        'event_id_new' => null,
        'created_at' => $date,
    ]);

    $this->get(route('reports.direct', ['date' => $date]))
        ->assertSuccessful()
        ->assertSee('Ventas Directas');
});

it('downloads direct sales pdf', function () {
    $date = now()->format('Y-m-d');
    Sale::factory()->create([
        'is_direct_sale' => true,
        'event_id' => 'Venta Directa 1',
        'event_id_new' => null,
        'created_at' => $date,
    ]);

    $this->get(route('reports.direct.pdf', ['date' => $date]))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});
