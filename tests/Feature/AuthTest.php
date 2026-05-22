<?php

use App\Models\Staff;

use function Pest\Laravel\actingAs;

it('shows login form', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('Bienvenido');
});

it('can login with valid credentials', function () {
    $staff = Staff::factory()->create([
        'username' => 'admin',
        'password' => bcrypt('secret123'),
    ]);

    $this->post(route('login'), [
        'username' => 'admin',
        'password' => 'secret123',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($staff, 'staff');
});

it('fails with invalid password', function () {
    Staff::factory()->create([
        'username' => 'admin',
        'password' => bcrypt('secret123'),
    ]);

    $this->post(route('login'), [
        'username' => 'admin',
        'password' => 'wrongpassword',
    ])->assertSessionHasErrors('username');

    $this->assertGuest('staff');
});

it('fails with non-existent username', function () {
    $this->post(route('login'), [
        'username' => 'nobody',
        'password' => 'secret123',
    ])->assertSessionHasErrors('username');

    $this->assertGuest('staff');
});

it('can logout', function () {
    $staff = Staff::factory()->create();
    actingAs($staff, 'staff');

    $this->post(route('logout'))
        ->assertRedirect('/login');

    $this->assertGuest('staff');
});

it('redirects to login when not authenticated', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('requires username and password', function () {
    $this->post(route('login'), [])
        ->assertSessionHasErrors(['username', 'password']);
});
