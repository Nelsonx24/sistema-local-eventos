<?php

use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('loads settings page', function () {
    $this->get(route('settings.index'))
        ->assertSuccessful()
        ->assertSee('Configuración');
});

it('can update settings', function () {
    $this->put(route('settings.update'), [
        'site_name' => 'Gran Cañaveral Test',
        'site_phone' => '78901234',
    ])->assertRedirect();

    $this->assertDatabaseHas('config', [
        'key' => 'site_name',
        'value' => 'Gran Cañaveral Test',
    ]);
});
