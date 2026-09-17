<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_detail_lists_same_user_properties_in_score_order(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create([
            'transaction_type' => 'buy',
            'budget' => 3_500_000,
            'locations' => 'สุขุมวิท',
        ]);
        $high = Property::factory()->for($user)->create([
            'name' => 'ตรงมาก',
            'transaction_type' => 'sale',
            'price' => 3_500_000,
            'bedrooms' => 2,
            'location' => 'สุขุมวิท',
        ]);
        $low = Property::factory()->for($user)->create([
            'name' => 'ตรงน้อย',
            'transaction_type' => 'rent',
            'price' => 7_000_000,
            'bedrooms' => 1,
            'location' => 'อารีย์',
        ]);

        $response = $this->actingAs($user)->get(route('clients.show', $client));

        $response->assertOk()->assertSee('ทรัพย์ที่ตรงกับลูกค้าคนนี้');
        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'ตรงน้อย'), strpos($content, 'ตรงมาก'));
        $response->assertSee('Match 100%')->assertSee('Match 50%')->assertSee('data-share-form', false)->assertSee('data-share-name="ตรงมาก"', false);
    }

    public function test_property_detail_lists_only_same_user_clients(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $property = Property::factory()->for($owner)->create([
            'name' => 'ทรัพย์ของฉัน',
            'transaction_type' => 'sale',
            'price' => 3_500_000,
            'location' => 'สุขุมวิท',
        ]);
        Client::factory()->for($owner)->create([
            'name' => 'ลูกค้าตรงมาก',
            'transaction_type' => 'buy',
            'budget' => 3_500_000,
            'locations' => 'สุขุมวิท',
        ]);
        Client::factory()->for($owner)->create([
            'name' => 'ลูกค้าตรงน้อย',
            'transaction_type' => 'rent',
            'budget' => 1_000_000,
            'locations' => 'อารีย์',
        ]);
        Client::factory()->for($other)->create(['name' => 'ลูกค้าคนอื่น']);

        $response = $this->actingAs($owner)->get(route('properties.show', $property));

        $response->assertOk()
            ->assertSee('ลูกค้าที่ตรงกับทรัพย์นี้')
            ->assertSee('ลูกค้าตรงมาก')
            ->assertSee('ลูกค้าตรงน้อย')
            ->assertDontSee('ลูกค้าคนอื่น');
        $content = $response->getContent();
        $this->assertLessThan(strpos($content, 'ลูกค้าตรงน้อย'), strpos($content, 'ลูกค้าตรงมาก'));
    }

    public function test_client_detail_does_not_match_properties_owned_by_another_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $client = Client::factory()->for($owner)->create(['name' => 'ลูกค้าของฉัน']);
        Property::factory()->for($other)->create(['name' => 'ทรัพย์ของคนอื่น']);

        $this->actingAs($owner)->get(route('clients.show', $client))
            ->assertOk()
            ->assertSee('ทรัพย์ที่ตรงกับลูกค้าคนนี้')
            ->assertDontSee('ทรัพย์ของคนอื่น');
    }

    public function test_guest_cannot_open_matching_contexts(): void
    {
        $property = Property::factory()->create();
        $client = Client::factory()->create();

        $this->get(route('properties.show', $property))->assertRedirect(route('login'));
        $this->get(route('clients.show', $client))->assertRedirect(route('login'));
    }
}
