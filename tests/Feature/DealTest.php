<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Deal;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealTest extends TestCase
{
    use RefreshDatabase;

    public function test_deal_progression_and_commission_are_deterministic(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();
        $property = Property::factory()->for($user)->create();
        $payload = ['stage' => 'new', 'property_id' => $property->id, 'amount' => 4_500_000, 'commission_rate' => 3, 'co_agent_split' => 50];
        $this->actingAs($user)->post(route('clients.deals.store', $client), $payload)->assertRedirect();
        $deal = Deal::query()->firstOrFail();
        $this->assertSame(135_000.0, $deal->grossCommission());
        $this->assertSame(67_500.0, $deal->agentCommission());
        $this->actingAs($user)->put(route('deals.update', $deal), [...$payload, 'stage' => 'closed'])->assertRedirect(route('deals.show', $deal));
        $this->assertDatabaseHas('deals', ['id' => $deal->id, 'stage' => 'closed']);
    }

    public function test_free_plan_blocks_second_active_deal_without_losing_first(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();
        $first = $user->deals()->create(['client_id' => $client->id, 'stage' => 'new', 'co_agent_split' => 0]);
        $this->actingAs($user)->post(route('clients.deals.store', $client), ['stage' => 'talking'])->assertRedirect(route('clients.deals.create', $client))->assertSessionHas('limit_reached');
        $this->assertDatabaseHas('deals', ['id' => $first->id, 'stage' => 'new']);
        $this->assertDatabaseCount('deals', 1);
    }

    public function test_deal_ownership_isolation(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $client = Client::factory()->for($owner)->create();
        $deal = $owner->deals()->create(['client_id' => $client->id, 'stage' => 'new', 'co_agent_split' => 0]);
        $this->actingAs($other)->get(route('deals.show', $deal))->assertForbidden();
        $this->actingAs($other)->get(route('deals.edit', $deal))->assertForbidden();
    }
}
