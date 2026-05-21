<?php

use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('lists staff', function () {
    Staff::factory(3)->create();

    $this->get(route('staff.index'))
        ->assertSuccessful()
        ->assertSee('Personal');
});

it('can create staff member', function () {
    $data = [
        'first_name' => 'Carlos',
        'last_name' => 'Mendoza',
        'username' => 'cmendoza',
        'password' => 'secret123',
        'role' => 'Vendedor',
        'email' => 'carlos@example.com',
    ];

    $this->post(route('staff.store'), $data)
        ->assertRedirect(route('staff.index'));

    $this->assertDatabaseHas('staff', ['username' => 'cmendoza']);
});

it('requires first_name to create staff', function () {
    $this->post(route('staff.store'), [
        'last_name' => 'User',
        'email' => 'test@example.com',
        'role' => 'Vendedor',
        'password' => 'password',
    ])->assertSessionHasErrors('first_name');
});

it('can view staff details', function () {
    $staff = Staff::factory()->create();

    $this->get(route('staff.show', $staff))
        ->assertSuccessful();
});

it('can edit staff', function () {
    $staff = Staff::factory()->create();

    $this->get(route('staff.edit', $staff))
        ->assertSuccessful();
});

it('can update staff', function () {
    $staff = Staff::factory()->create();

    $this->put(route('staff.update', $staff), [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'username' => $staff->username,
        'role' => 'Administrador',
        'email' => $staff->email,
    ])->assertRedirect(route('staff.index'));

    $this->assertDatabaseHas('staff', [
        'id' => $staff->id,
        'first_name' => 'Updated',
    ]);
});

it('can change staff password', function () {
    $staff = Staff::factory()->create();

    $this->post(route('staff.password', $staff), [
        'password' => 'newpassword123',
    ])->assertRedirect(route('staff.index'));
});

it('can delete staff', function () {
    $staff = Staff::factory()->create();

    $this->delete(route('staff.destroy', $staff))
        ->assertRedirect(route('staff.index'));

    $this->assertSoftDeleted($staff);
});
