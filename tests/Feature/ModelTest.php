<?php

use App\Models\Asset;
use App\Models\Config;
use App\Models\Event;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Staff;

// --- Staff ---

it('detects admin role', function () {
    $admin = Staff::factory()->admin()->create();

    expect($admin->isAdmin())->toBeTrue();
    expect($admin->isSeller())->toBeFalse();
    expect($admin->isCM())->toBeFalse();
    expect($admin->canAccess())->toBeTrue();
});

it('detects seller role', function () {
    $seller = Staff::factory()->create(['role' => 'Vendedor']);

    expect($seller->isSeller())->toBeTrue();
    expect($seller->isAdmin())->toBeFalse();
    expect($seller->canAccess())->toBeTrue();
});

it('detects cm role', function () {
    $cm = Staff::factory()->create(['role' => 'CM']);

    expect($cm->isCM())->toBeTrue();
    expect($cm->isAdmin())->toBeFalse();
    expect($cm->canAccess())->toBeTrue();
});

it('returns role label', function () {
    expect(Staff::factory()->admin()->create()->role_label)->toBe('Administrador');
    expect(Staff::factory()->create(['role' => 'Vendedor'])->role_label)->toBe('Vendedor');
    expect(Staff::factory()->create(['role' => 'CM'])->role_label)->toBe('CM');
    expect(Staff::factory()->create(['role' => 'Otro'])->role_label)->toBe('Personal');
});

// --- Event ---

it('detects event status', function () {
    $confirmed = Event::factory()->create(['status' => 'Confirmado']);
    expect($confirmed->isConfirmed())->toBeTrue();
    expect($confirmed->isPending())->toBeFalse();

    $pending = Event::factory()->create(['status' => 'Pendiente']);
    expect($pending->isPending())->toBeTrue();

    $cancelled = Event::factory()->create(['status' => 'Cancelado']);
    expect($cancelled->isCancelled())->toBeTrue();

    $closed = Event::factory()->create(['status' => 'Cerrado']);
    expect($closed->isClosed())->toBeTrue();
});

it('detects paid event', function () {
    $paid = Event::factory()->create(['balance_pending' => 0]);
    expect($paid->isPaid())->toBeTrue();

    $unpaid = Event::factory()->create(['balance_pending' => 500]);
    expect($unpaid->isPaid())->toBeFalse();
});

it('returns correct status color', function () {
    expect(Event::factory()->create(['status' => 'Confirmado'])->status_color)->toBe('emerald');
    expect(Event::factory()->create(['status' => 'Pendiente'])->status_color)->toBe('amber');
    expect(Event::factory()->create(['status' => 'Cancelado'])->status_color)->toBe('red');
    expect(Event::factory()->create(['status' => 'Cerrado'])->status_color)->toBe('slate');
    expect(Event::factory()->create(['status' => 'Otro'])->status_color)->toBe('gray');
});

it('uppercases client name', function () {
    $event = Event::factory()->create(['client_name' => 'Juan Pérez']);

    expect($event->client_name)->toBe('JUAN PÉREZ');
});

// --- Inventory ---

it('detects low stock', function () {
    expect(Inventory::factory()->create(['boxes' => 1])->isLowStock())->toBeTrue();
    expect(Inventory::factory()->create(['boxes' => 2])->isLowStock())->toBeTrue();
    expect(Inventory::factory()->create(['boxes' => 3])->isLowStock())->toBeFalse();
});

it('calculates total units', function () {
    $item = Inventory::factory()->create([
        'boxes' => 5,
        'units_per_box' => 12,
        'loose_units' => 3,
    ]);

    expect($item->total_units)->toBe(63);
});

it('subtracts box stock', function () {
    $item = Inventory::factory()->create(['boxes' => 10, 'units_per_box' => 12, 'loose_units' => 5]);
    $item->subtractStock(3, 'Caja');

    expect($item->fresh()->boxes)->toBe(7);
    expect($item->fresh()->loose_units)->toBe(5);
});

it('subtracts loose units directly when enough available', function () {
    $item = Inventory::factory()->create(['boxes' => 10, 'units_per_box' => 12, 'loose_units' => 10]);
    $item->subtractStock(3, 'Unidad');

    expect($item->fresh()->loose_units)->toBe(7);
});

it('opens a box when loose units are insufficient', function () {
    $item = Inventory::factory()->create(['boxes' => 5, 'units_per_box' => 12, 'loose_units' => 2]);
    $item->subtractStock(10, 'Unidad');

    expect($item->fresh()->boxes)->toBe(4);
    expect($item->fresh()->loose_units)->toBe(4);
});

it('subtracts stock without going negative', function () {
    $item = Inventory::factory()->create(['boxes' => 0, 'units_per_box' => 12, 'loose_units' => 1]);
    $item->subtractStock(100, 'Unidad');

    expect($item->fresh()->loose_units)->toBe(0);
    expect($item->fresh()->boxes)->toBe(0);
});

// --- Sale ---

it('calculates total amount from items', function () {
    $sale = Sale::factory()->create();
    SaleItem::create(['sale_id' => $sale->id, 'name' => 'Item 1', 'quantity' => 2, 'type' => 'Caja', 'subtotal' => 100]);
    SaleItem::create(['sale_id' => $sale->id, 'name' => 'Item 2', 'quantity' => 1, 'type' => 'Unidad', 'subtotal' => 50]);

    expect($sale->total_amount)->toBe(150.0);
});

it('marks sale as printed', function () {
    $sale = Sale::factory()->create(['is_printed' => false]);
    $sale->markAsPrinted();

    expect($sale->fresh()->is_printed)->toBeTrue();
});

it('uppercases sale client name', function () {
    $sale = Sale::factory()->create(['client_name' => 'María García']);

    expect($sale->client_name)->toBe('MARÍA GARCÍA');
});

// --- Asset ---

it('detects good condition', function () {
    expect(Asset::factory()->create(['condition' => 'Bueno'])->isInGoodCondition())->toBeTrue();
    expect(Asset::factory()->create(['condition' => 'Regular'])->isInGoodCondition())->toBeFalse();
    expect(Asset::factory()->create(['condition' => 'Malo'])->isInGoodCondition())->toBeFalse();
});

it('detects maintenance need', function () {
    expect(Asset::factory()->create(['condition' => 'Malo'])->needsMaintenance())->toBeTrue();
    expect(Asset::factory()->create(['condition' => 'Regular'])->needsMaintenance())->toBeTrue();
    expect(Asset::factory()->create(['condition' => 'Bueno'])->needsMaintenance())->toBeFalse();
});

// --- Config ---

it('stores and retrieves config values', function () {
    Config::set('test_key', 'test_value');

    expect(Config::get('test_key'))->toBe('test_value');
});

it('returns default for missing config', function () {
    expect(Config::get('non_existent', 'default_value'))->toBe('default_value');
});

it('manages qr', function () {
    Config::setQR('http://example.com/qr.png');

    expect(Config::getQR())->toBe('http://example.com/qr.png');
});

it('manages event types', function () {
    Config::setEventTypes(['Boda', 'Fiesta']);

    expect(Config::getEventTypes())->toBe(['Boda', 'Fiesta']);
});

it('returns default event types', function () {
    Config::where('key', 'event_types')->delete();

    expect(Config::getEventTypes())->toBe(['Boda', 'Corporativo', 'Cumpleaños', 'Social']);
});

it('manages contract settings', function () {
    $settings = ['salon_name' => 'Mi Salón', 'representative' => 'Yo', 'representative_ci' => '1234', 'city' => 'LP'];
    Config::setContractSettings($settings);

    expect(Config::getContractSettings())->toBe($settings);
});

it('manages watermark', function () {
    Config::setWatermark('path/to/image.png');

    expect(Config::getWatermark())->toBe('path/to/image.png');
});
