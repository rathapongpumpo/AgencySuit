<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\FollowUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_schedule_quick_follow_up_and_today_separates_overdue_and_due(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        $this->actingAs($user)->post(route('clients.followups.store', $client), ['days' => 1, 'note' => 'ส่งรายการใหม่'])
            ->assertRedirect(route('clients.show', $client));
        $today = $user->followUps()->create(['client_id' => $client->id, 'due_date' => today(), 'status' => 'pending']);
        $overdue = $user->followUps()->create(['client_id' => $client->id, 'due_date' => today()->subDay(), 'status' => 'pending']);

        $this->assertDatabaseHas('follow_ups', ['client_id' => $client->id, 'due_date' => today()->addDay()->toDateString(), 'status' => 'pending']);
        $response = $this->actingAs($user)->get(route('today'));
        $response->assertOk()->assertSee('เลยกำหนด')->assertSee('ต้องติดตามวันนี้')->assertSee($client->name);
    }

    public function test_owner_can_mark_follow_up_done_and_other_user_cannot(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $client = Client::factory()->for($owner)->create();
        $followUp = $owner->followUps()->create(['client_id' => $client->id, 'due_date' => today(), 'status' => 'pending']);

        $this->actingAs($other)->patch(route('followups.complete', $followUp))->assertForbidden();
        $this->actingAs($owner)->patch(route('followups.complete', $followUp))->assertRedirect();
        $this->assertDatabaseHas('follow_ups', ['id' => $followUp->id, 'status' => 'completed']);
    }

    public function test_guest_cannot_use_follow_up_routes(): void
    {
        $client = Client::factory()->create();
        $followUp = FollowUp::create(['user_id' => $client->user_id, 'client_id' => $client->id, 'due_date' => today(), 'status' => 'pending']);

        $this->post(route('clients.followups.store', $client), ['days' => 1])->assertRedirect(route('login'));
        $this->patch(route('followups.complete', $followUp))->assertRedirect(route('login'));
    }
}
