<?php

use App\Models\Log;
use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('lists logs', function () {
    Log::create([
        'type' => 'Evento',
        'action' => 'Crear',
        'description' => 'Se creó un evento',
        'user_id' => $this->admin->id,
        'user_name' => $this->admin->name,
    ]);

    $this->get(route('logs.index'))
        ->assertSuccessful()
        ->assertSee('Logs');
});

it('can filter logs by type', function () {
    Log::create([
        'type' => 'Evento',
        'action' => 'Crear',
        'description' => 'Evento test',
        'user_id' => $this->admin->id,
        'user_name' => $this->admin->name,
    ]);

    $this->get(route('logs.index', ['type' => 'Evento']))
        ->assertSuccessful()
        ->assertSee('Evento test');
});

it('can filter logs by action', function () {
    Log::create([
        'type' => 'Venta',
        'action' => 'Eliminar',
        'description' => 'Venta eliminada',
        'user_id' => $this->admin->id,
        'user_name' => $this->admin->name,
    ]);

    $this->get(route('logs.index', ['action' => 'Eliminar']))
        ->assertSuccessful()
        ->assertSee('Venta eliminada');
});
