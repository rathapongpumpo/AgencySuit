<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_client_with_minimum_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('clients.store'), $this->validPayload());

        $client = Client::query()->firstOrFail();

        $response->assertRedirect(route('clients.show', $client));
        $this->assertSame($user->id, $client->user_id);
        $this->assertDatabaseHas('clients', [
            'name' => 'คุณเมย์',
            'transaction_type' => 'buy',
            'budget' => '3500000.00',
            'locations' => 'สุขุมวิท, อโศก',
        ]);
    }

    public function test_client_core_fields_are_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('clients.store'), [
            'name' => '',
            'transaction_type' => 'invalid',
            'budget' => 'not-a-number',
            'locations' => '',
        ])->assertSessionHasErrors(['name', 'transaction_type', 'budget', 'locations']);

        $this->assertDatabaseCount('clients', 0);
    }

    public function test_user_can_list_and_view_client_detail_without_empty_optional_labels(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create([
            'name' => 'คุณบีม',
            'transaction_type' => 'rent',
            'budget' => 18000,
            'locations' => 'อารีย์',
        ]);

        $this->actingAs($user)->get(route('clients.index'))
            ->assertOk()
            ->assertDontSee('ยังไม่มีลูกค้าในระบบ')
            ->assertSee('คุณบีม')
            ->assertSee('เช่า')
            ->assertSee('18,000 บาท')
            ->assertSee('อารีย์')
            ->assertDontSee('เบอร์โทร');

        $this->actingAs($user)->get(route('clients.show', $client))
            ->assertOk()
            ->assertSee('คุณบีม')
            ->assertSee('เช่า')
            ->assertSee('18,000 บาท')
            ->assertSee('แก้ไข')
            ->assertDontSee('ข้อมูลติดต่อ');
    }

    public function test_user_can_edit_client_core_fields(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        $this->actingAs($user)->put(route('clients.update', $client), $this->validPayload([
            'name' => 'คุณใหม่',
            'transaction_type' => 'rent',
            'budget' => 25000,
            'locations' => 'ทองหล่อ',
        ]))->assertRedirect(route('clients.show', $client));

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'คุณใหม่',
            'transaction_type' => 'rent',
            'budget' => '25000.00',
            'locations' => 'ทองหล่อ',
        ]);
    }

    public function test_user_can_edit_optional_client_details(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->for($user)->create();

        $this->actingAs($user)->put(route('clients.update', $client), $this->validPayload([
            'phone' => '0812345678',
            'contact_channel' => 'line-mei',
            'bedrooms' => 2,
            'minimum_size' => 35.5,
            'transit_preference' => 'ใกล้ BTS อโศก',
            'notes' => 'ขอห้องไม่ติดถนน',
        ]))->assertRedirect(route('clients.show', $client));

        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'phone' => '0812345678',
            'contact_channel' => 'line-mei',
            'bedrooms' => 2,
            'minimum_size' => '35.50',
            'transit_preference' => 'ใกล้ BTS อโศก',
            'notes' => 'ขอห้องไม่ติดถนน',
        ]);

        $this->actingAs($user)->get(route('clients.show', $client->fresh()))
            ->assertSee('tel:0812345678', false)
            ->assertSee('line-mei')
            ->assertSee('ใกล้ BTS อโศก')
            ->assertSee('ขอห้องไม่ติดถนน');
    }

    public function test_other_users_cannot_access_or_change_client(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $client = Client::factory()->for($owner)->create(['name' => 'ข้อมูลส่วนตัว']);

        $this->actingAs($otherUser)->get(route('clients.index'))
            ->assertOk()
            ->assertDontSee('ข้อมูลส่วนตัว');
        $this->actingAs($otherUser)->get(route('clients.show', $client))->assertForbidden();
        $this->actingAs($otherUser)->get(route('clients.edit', $client))->assertForbidden();
        $this->actingAs($otherUser)->put(route('clients.update', $client), $this->validPayload())->assertForbidden();
    }

    public function test_free_plan_limit_blocks_sixth_client_and_preserves_existing_data(): void
    {
        $user = User::factory()->create();
        $limit = (int) config('plans.free.limits.clients');
        $clients = Client::factory()->count($limit)->for($user)->create();

        $this->actingAs($user)->get(route('clients.create'))
            ->assertOk()
            ->assertSee("แพ็กเกจฟรีเพิ่มลูกค้าได้สูงสุด {$limit} รายการ")
            ->assertSee('ข้อมูลเดิมยังอยู่ครบ');

        $this->actingAs($user)->post(route('clients.store'), $this->validPayload([
            'name' => 'รายการที่เกินลิมิต',
        ]))->assertRedirect(route('clients.create'))
            ->assertSessionHas('limit_reached', "แพ็กเกจฟรีเพิ่มลูกค้าได้สูงสุด {$limit} รายการ ข้อมูลเดิมยังอยู่ครบ");

        $this->assertDatabaseCount('clients', $limit);
        $this->assertDatabaseHas('clients', ['id' => $clients->first()->id]);
    }

    public function test_guest_is_protected_from_client_routes(): void
    {
        $client = Client::factory()->create();

        foreach ([
            ['get', route('clients.index')],
            ['get', route('clients.create')],
            ['get', route('clients.show', $client)],
            ['get', route('clients.edit', $client)],
            ['post', route('clients.store')],
        ] as [$method, $url]) {
            $this->{$method}($url)->assertRedirect(route('login'));
        }
    }

    public function test_quick_add_client_action_links_to_client_quick_add(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('today'))
            ->assertOk()
            ->assertSee(route('clients.create'), false);
    }

    /** @param array<string, mixed> $overrides */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'คุณเมย์',
            'transaction_type' => 'buy',
            'budget' => 3500000,
            'locations' => 'สุขุมวิท, อโศก',
        ], $overrides);
    }
}
