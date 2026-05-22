<?php

use App\Models\Asset;
use App\Models\Config;
use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('loads others menu', function () {
    $this->get(route('others.index'))
        ->assertSuccessful()
        ->assertSee('Otros');
});

// --- QR ---

it('loads qr page', function () {
    Config::setQR('http://example.com/qr.png');

    $this->get(route('others.qr'))
        ->assertSuccessful()
        ->assertSee('QR');
});

it('can update qr with url', function () {
    $this->post(route('others.qr.update'), [
        'qr_url' => 'http://example.com/new-qr.png',
    ])->assertRedirect();
});

// --- Assets ---

it('lists assets', function () {
    Asset::factory(3)->create();

    $this->get(route('others.assets'))
        ->assertSuccessful()
        ->assertSee('Activos');
});

it('can create an asset', function () {
    $this->post(route('others.assets.store'), [
        'name' => 'Mesa Plegable',
        'category' => 'Mobiliario',
        'quantity' => 10,
        'condition' => 'Bueno',
    ])->assertRedirect();

    $this->assertDatabaseHas('assets', ['name' => 'Mesa Plegable']);
});

it('requires name to create asset', function () {
    $this->post(route('others.assets.store'), [
        'category' => 'Mobiliario',
        'quantity' => 5,
        'condition' => 'Bueno',
    ])->assertSessionHasErrors('name');
});

it('can delete an asset', function () {
    $asset = Asset::factory()->create();

    $this->delete(route('others.assets.destroy', $asset))
        ->assertRedirect();

    $this->assertSoftDeleted($asset);
});

// --- Contract Settings ---

it('loads contract settings page', function () {
    $this->get(route('others.contract-settings'))
        ->assertSuccessful()
        ->assertSee('Contrato');
});

it('can update contract settings', function () {
    $this->post(route('others.contract-settings.update'), [
        'salon_name' => 'Salón Test',
        'representative' => 'Juan Pérez',
        'representative_ci' => '12345678',
        'city' => 'Santa Cruz',
    ])->assertRedirect();
});

it('requires contract settings fields', function () {
    $this->post(route('others.contract-settings.update'), [])
        ->assertSessionHasErrors(['salon_name', 'representative', 'representative_ci', 'city']);
});

// --- Notifications ---

it('loads notifications page', function () {
    $this->get(route('others.notifications'))
        ->assertSuccessful()
        ->assertSee('Notificaciones');
});
