<?php

use App\Models\Staff;
use App\Models\StoreGift;
use App\Models\StoreGiftType;
use App\Models\StoreProduct;
use App\Models\StoreProductType;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('loads store index', function () {
    StoreProduct::factory(2)->create();
    StoreGift::factory(2)->create();

    $this->get(route('store.index'))
        ->assertSuccessful()
        ->assertSee('Tienda');
});

// --- Products ---

it('lists products', function () {
    StoreProduct::factory(3)->create();

    $this->get(route('store.products'))
        ->assertSuccessful()
        ->assertSee('Productos');
});

it('shows product creation form', function () {
    StoreProductType::factory()->create(['name' => 'TestType']);

    $this->get(route('store.products.create'))
        ->assertSuccessful()
        ->assertSee('TestType');
});

it('can create a product', function () {
    $type = StoreProductType::factory()->create();

    $this->post(route('store.products.store'), [
        'name' => 'Test Product',
        'cost' => 20,
        'sale_price' => 40,
        'stock' => 10,
        'category' => 'Bebidas',
        'product_type_id' => $type->id,
    ])->assertRedirect(route('store.products'));

    $this->assertDatabaseHas('store_products', ['name' => 'Test Product']);
});

it('requires name to create product', function () {
    $this->post(route('store.products.store'), [
        'cost' => 20,
        'sale_price' => 40,
        'stock' => 10,
        'category' => 'Bebidas',
    ])->assertSessionHasErrors('name');
});

it('shows a product', function () {
    $product = StoreProduct::factory()->create();

    $this->get(route('store.products.show', $product))
        ->assertSuccessful()
        ->assertJson(['id' => $product->id]);
});

it('shows product edit form', function () {
    $product = StoreProduct::factory()->create();

    $this->get(route('store.products.edit', $product))
        ->assertSuccessful()
        ->assertSee($product->name);
});

it('can update a product', function () {
    $product = StoreProduct::factory()->create();

    $this->put(route('store.products.update', $product), [
        'name' => 'Updated Product',
        'cost' => 25,
        'sale_price' => 50,
        'stock' => 20,
        'category' => 'Snacks',
        'product_type_id' => $product->product_type_id,
    ])->assertRedirect(route('store.products'));

    $this->assertDatabaseHas('store_products', [
        'id' => $product->id,
        'name' => 'Updated Product',
    ]);
});

it('can delete a product', function () {
    $product = StoreProduct::factory()->create();

    $this->delete(route('store.products.destroy', $product))
        ->assertRedirect(route('store.products'));

    $this->assertDatabaseMissing('store_products', ['id' => $product->id]);
});

it('lists product sales', function () {
    $product = StoreProduct::factory()->create(['stock' => 10]);

    $this->get(route('store.products.sales'))
        ->assertSuccessful()
        ->assertSee('Ventas');
});

it('can process a product sale', function () {
    $product = StoreProduct::factory()->create(['stock' => 10, 'cost' => 20, 'sale_price' => 40]);

    $this->post(route('store.products.sale.process'), [
        'store_product_id' => $product->id,
        'quantity' => 2,
    ])->assertRedirect(route('store.products.sales'));

    $this->assertDatabaseHas('store_products', [
        'id' => $product->id,
        'stock' => 8,
    ]);

    $this->assertDatabaseHas('store_sales', [
        'store_product_id' => $product->id,
        'quantity' => 2,
    ]);
});

it('fails product sale with insufficient stock', function () {
    $product = StoreProduct::factory()->create(['stock' => 1]);

    $this->post(route('store.products.sale.process'), [
        'store_product_id' => $product->id,
        'quantity' => 10,
    ])->assertSessionHasErrors('quantity');

    $this->assertDatabaseHas('store_products', [
        'id' => $product->id,
        'stock' => 1,
    ]);
});

it('can add stock to a product', function () {
    $product = StoreProduct::factory()->create(['stock' => 5]);

    $this->post(route('store.products.add-stock'), [
        'product_id' => $product->id,
        'quantity' => 10,
    ])->assertRedirect();

    $this->assertDatabaseHas('store_products', [
        'id' => $product->id,
        'stock' => 15,
    ]);
});

it('can create a product type', function () {
    $this->post(route('store.products.types.store'), [
        'name' => 'Nuevo Tipo',
    ])->assertRedirect();

    $this->assertDatabaseHas('store_product_types', ['name' => 'Nuevo Tipo']);
});

it('can delete a product type', function () {
    $type = StoreProductType::factory()->create();

    $this->post(route('store.products.types.destroy', $type))
        ->assertRedirect();

    $this->assertDatabaseMissing('store_product_types', ['id' => $type->id]);
});

// --- Gifts ---

it('lists gifts', function () {
    StoreGift::factory(3)->create();

    $this->get(route('store.gifts'))
        ->assertSuccessful()
        ->assertSee('Regalos');
});

it('shows gift creation form', function () {
    StoreGiftType::factory()->create(['name' => 'GiftType']);

    $this->get(route('store.gifts.create'))
        ->assertSuccessful()
        ->assertSee('GiftType');
});

it('can create a gift', function () {
    $type = StoreGiftType::factory()->create();

    $this->post(route('store.gifts.store'), [
        'name' => 'Test Gift',
        'cost' => 15,
        'sale_price' => 30,
        'stock' => 10,
        'category' => 'Juguetes',
        'gift_type_id' => $type->id,
    ])->assertRedirect(route('store.gifts'));

    $this->assertDatabaseHas('store_gifts', ['name' => 'Test Gift']);
});

it('requires name to create gift', function () {
    $this->post(route('store.gifts.store'), [
        'cost' => 15,
        'sale_price' => 30,
        'stock' => 10,
        'category' => 'Juguetes',
    ])->assertSessionHasErrors('name');
});

it('shows a gift', function () {
    $gift = StoreGift::factory()->create();

    $this->get(route('store.gifts.show', $gift))
        ->assertSuccessful()
        ->assertJson(['id' => $gift->id]);
});

it('shows gift edit form', function () {
    $gift = StoreGift::factory()->create();

    $this->get(route('store.gifts.edit', $gift))
        ->assertSuccessful()
        ->assertSee($gift->name);
});

it('can update a gift', function () {
    $gift = StoreGift::factory()->create();

    $this->put(route('store.gifts.update', $gift), [
        'name' => 'Updated Gift',
        'cost' => 18,
        'sale_price' => 36,
        'stock' => 15,
        'category' => 'Decoración',
        'gift_type_id' => $gift->gift_type_id,
    ])->assertRedirect(route('store.gifts'));

    $this->assertDatabaseHas('store_gifts', [
        'id' => $gift->id,
        'name' => 'Updated Gift',
    ]);
});

it('can delete a gift', function () {
    $gift = StoreGift::factory()->create();

    $this->delete(route('store.gifts.destroy', $gift))
        ->assertRedirect(route('store.gifts'));

    $this->assertDatabaseMissing('store_gifts', ['id' => $gift->id]);
});

it('lists gift sales', function () {
    $gift = StoreGift::factory()->create(['stock' => 10]);

    $this->get(route('store.gifts.sales'))
        ->assertSuccessful()
        ->assertSee('Ventas');
});

it('can process a gift sale', function () {
    $gift = StoreGift::factory()->create(['stock' => 10, 'cost' => 10, 'sale_price' => 25]);

    $this->post(route('store.gifts.sale.process'), [
        'store_gift_id' => $gift->id,
        'quantity' => 3,
    ])->assertRedirect(route('store.gifts.sales'));

    $this->assertDatabaseHas('store_gifts', [
        'id' => $gift->id,
        'stock' => 7,
    ]);

    $this->assertDatabaseHas('store_gift_sales', [
        'store_gift_id' => $gift->id,
        'quantity' => 3,
    ]);
});

it('fails gift sale with insufficient stock', function () {
    $gift = StoreGift::factory()->create(['stock' => 1]);

    $this->post(route('store.gifts.sale.process'), [
        'store_gift_id' => $gift->id,
        'quantity' => 10,
    ])->assertSessionHasErrors('quantity');

    $this->assertDatabaseHas('store_gifts', [
        'id' => $gift->id,
        'stock' => 1,
    ]);
});

it('can add stock to a gift', function () {
    $gift = StoreGift::factory()->create(['stock' => 5]);

    $this->post(route('store.gifts.add-stock'), [
        'gift_id' => $gift->id,
        'quantity' => 10,
    ])->assertRedirect();

    $this->assertDatabaseHas('store_gifts', [
        'id' => $gift->id,
        'stock' => 15,
    ]);
});

it('can create a gift type', function () {
    $this->post(route('store.gifts.types.store'), [
        'name' => 'Nuevo GiftType',
    ])->assertRedirect();

    $this->assertDatabaseHas('store_gift_types', ['name' => 'Nuevo GiftType']);
});

it('can delete a gift type', function () {
    $type = StoreGiftType::factory()->create();

    $this->post(route('store.gifts.types.destroy', $type))
        ->assertRedirect();

    $this->assertDatabaseMissing('store_gift_types', ['id' => $type->id]);
});
