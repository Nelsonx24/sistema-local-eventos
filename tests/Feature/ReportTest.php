<?php

use App\Models\Event;
use App\Models\Sale;
use App\Models\Staff;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->admin = Staff::factory()->admin()->create();
    actingAs($this->admin, 'staff');
});

it('lists reports', function () {
    Event::factory(3)->completed()->create();

    $this->get(route('reports.index'))
        ->assertSuccessful()
        ->assertSee('Reportes');
});

it('shows event report detail', function () {
    $event = Event::factory()->completed()->create();
    Sale::factory(2)->create(['event_id' => $event->id]);

    $this->get(route('reports.show', $event))
        ->assertSuccessful()
        ->assertSee($event->client_name);
});

it('shows totals on report index', function () {
    $event = Event::factory()->completed()->create(['total_amount' => 5000]);
    Sale::factory()->create(['event_id' => $event->id, 'amount' => 1000]);

    $this->get(route('reports.index'))
        ->assertSuccessful();
});

it('can download event report pdf', function () {
    $event = Event::factory()->completed()->create();

    $this->get(route('reports.pdf', $event))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

it('can delete event from reports', function () {
    $event = Event::factory()->completed()->create();

    $this->delete(route('reports.destroy', $event))
        ->assertRedirect(route('reports.index'));

    $this->assertSoftDeleted($event);
});
