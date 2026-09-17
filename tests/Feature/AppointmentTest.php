<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_edit_cancel_and_see_appointment_on_today(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();
        $property = Property::factory()->for($user)->create();
        $payload = ['client_id' => $client->id, 'property_id' => $property->id, 'appointment_date' => today()->toDateString(), 'appointment_time' => '14:30', 'note' => 'พาชมส่วนกลาง'];

        $response = $this->actingAs($user)->post(route('appointments.store'), $payload);
        $appointment = Appointment::query()->firstOrFail();
        $response->assertRedirect(route('appointments.show', $appointment));
        $this->actingAs($user)->get(route('today'))->assertSee('นัดวันนี้')->assertSee($client->name)->assertSee($property->name);
        $this->actingAs($user)->put(route('appointments.update', $appointment), [...$payload, 'appointment_time' => '15:00'])->assertRedirect(route('appointments.show', $appointment));
        $this->actingAs($user)->patch(route('appointments.cancel', $appointment))->assertRedirect(route('appointments.show', $appointment));
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'appointment_time' => '15:00:00', 'status' => 'cancelled']);
    }

    public function test_appointment_ownership_isolation_and_guest_protection(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $client = Client::factory()->for($owner)->create();
        $property = Property::factory()->for($owner)->create();
        $appointment = $owner->appointments()->create(['client_id' => $client->id, 'property_id' => $property->id, 'appointment_date' => today(), 'appointment_time' => '10:00', 'status' => 'scheduled']);

        $this->actingAs($other)->get(route('appointments.show', $appointment))->assertForbidden();
        $this->actingAs($other)->get(route('appointments.edit', $appointment))->assertForbidden();
        $this->actingAs($other)->patch(route('appointments.cancel', $appointment))->assertForbidden();
        Auth::logout();
        $this->get(route('appointments.create'))->assertRedirect(route('login'));
    }
}
