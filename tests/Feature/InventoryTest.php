<?php

use App\Models\Inventory;
use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('lists inventory', function () {
    Inventory::factory(3)->create();

    $this->get(route('inventory.index'))
        ->assertSuccessful()
        ->assertSee('Inventario');
});

it('can create inventory item', function () {
    $data = [
        'name' => 'TestProduct_620cc',
        'category' => 'Cerveza',
        'boxes' => 20,
        'units_per_box' => 12,
        'loose_units' => 5,
        'price_per_box' => 150,
        'price_per_unit' => 15,
    ];

    $this->post(route('inventory.store'), $data)
        ->assertRedirect(route('inventory.index'));

    $this->assertDatabaseHas('inventory', ['name' => 'TestProduct_620cc']);
});

it('requires name to create inventory', function () {
    $this->post(route('inventory.store'), [
        'category' => 'Cerveza',
        'boxes' => 10,
    ])->assertSessionHasErrors('name');
});

it('can update inventory', function () {
    $item = Inventory::factory()->create();

    $this->put(route('inventory.update', $item), [
        'name' => 'UpdatedProduct_620cc',
        'category' => 'Gaseosa',
        'boxes' => 30,
        'units_per_box' => 12,
        'loose_units' => 10,
        'price_per_box' => 200,
        'price_per_unit' => 20,
    ])->assertRedirect(route('inventory.index'));

    $this->assertDatabaseHas('inventory', [
        'id' => $item->id,
        'name' => 'UpdatedProduct_620cc',
    ]);
});

it('can restock inventory', function () {
    $item = Inventory::factory()->create(['boxes' => 5]);

    $this->post(route('inventory.restock'), [
        'inventory_id' => $item->id,
        'boxes_to_add' => 10,
        'loose_to_add' => 5,
    ])->assertRedirect();

    $this->assertDatabaseHas('inventory', [
        'id' => $item->id,
        'boxes' => 15,
    ]);
});

it('can delete inventory', function () {
    $item = Inventory::factory()->create();

    $this->delete(route('inventory.destroy', $item))
        ->assertRedirect(route('inventory.index'));

    $this->assertSoftDeleted($item);
});
