<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_property_with_minimum_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('properties.store'), $this->validPayload());

        $property = Property::query()->firstOrFail();

        $response->assertRedirect(route('properties.show', $property));
        $this->assertSame($user->id, $property->user_id);
        $this->assertSame(Property::DEFAULT_STATUS, $property->status);
        $this->assertSame('พร้อมขาย', $property->status_label);
        $this->assertDatabaseHas('properties', [
            'name' => 'สุขุมวิท เรสซิเดนซ์',
            'transaction_type' => 'sale',
            'price' => '3500000.00',
            'bedrooms' => 2,
            'location' => 'สุขุมวิท',
        ]);
    }

    public function test_quick_add_property_action_opens_the_property_quick_add_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('today'))
            ->assertOk()
            ->assertSee(route('properties.create'), false);
    }

    public function test_property_core_fields_are_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('properties.store'), [
            'transaction_type' => 'invalid',
            'name' => '',
            'price' => 'not-a-number',
            'bedrooms' => -1,
            'location' => '',
        ])->assertSessionHasErrors([
            'transaction_type',
            'name',
            'price',
            'bedrooms',
            'location',
        ]);

        $this->assertDatabaseCount('properties', 0);
    }

    public function test_user_can_list_and_view_property_detail(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->for($user)->create([
            'name' => 'อารีย์ เพลส',
            'transaction_type' => 'rent',
            'price' => 18000,
            'bedrooms' => 1,
            'location' => 'อารีย์',
        ]);

        $this->actingAs($user)->get(route('properties.index'))
            ->assertOk()
            ->assertDontSee('ยังไม่มีทรัพย์ในระบบ')
            ->assertSee('อารีย์ เพลส')
            ->assertSee('เช่า')
            ->assertSee('18,000 บาท')
            ->assertSee('อารีย์');

        $this->actingAs($user)->get(route('properties.show', $property))
            ->assertOk()
            ->assertSee('อารีย์ เพลส')
            ->assertSee('เช่า')
            ->assertSee('พร้อมเช่า')
            ->assertSee('แก้ไข')
            ->assertSee('เปลี่ยนสถานะ');
    }

    public function test_user_can_edit_property_core_fields(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->for($user)->create();

        $this->actingAs($user)->put(route('properties.update', $property), $this->validPayload([
            'transaction_type' => 'rent',
            'name' => 'ทองหล่อ ทาวเวอร์',
            'price' => 25000,
            'bedrooms' => 1,
            'location' => 'ทองหล่อ',
        ]))->assertRedirect(route('properties.show', $property));

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'transaction_type' => 'rent',
            'name' => 'ทองหล่อ ทาวเวอร์',
            'price' => '25000.00',
            'bedrooms' => 1,
            'location' => 'ทองหล่อ',
        ]);
    }

    public function test_user_can_change_property_status(): void
    {
        $user = User::factory()->create();
        $property = Property::factory()->for($user)->create();

        $this->actingAs($user)->patch(route('properties.status.update', $property), [
            'status' => 'reserved',
        ])->assertRedirect(route('properties.show', $property));

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => 'reserved',
        ]);
    }

    public function test_user_cannot_access_another_users_property(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $property = Property::factory()->for($owner)->create(['name' => 'ข้อมูลส่วนตัว']);

        $this->actingAs($otherUser)->get(route('properties.index'))
            ->assertOk()
            ->assertDontSee('ข้อมูลส่วนตัว');
        $this->actingAs($otherUser)->get(route('properties.show', $property))->assertForbidden();
        $this->actingAs($otherUser)->get(route('properties.edit', $property))->assertForbidden();
        $this->actingAs($otherUser)->put(route('properties.update', $property), $this->validPayload())->assertForbidden();
        $this->actingAs($otherUser)->patch(route('properties.status.update', $property), ['status' => 'paused'])->assertForbidden();
    }

    public function test_free_plan_limit_blocks_the_next_property_and_preserves_existing_data(): void
    {
        $user = User::factory()->create();
        $limit = (int) config('plans.free.limits.properties');
        $properties = Property::factory()->count($limit)->for($user)->create();

        $this->actingAs($user)->get(route('properties.create'))
            ->assertOk()
            ->assertSee("แพ็กเกจฟรีเพิ่มทรัพย์ได้สูงสุด {$limit} รายการ")
            ->assertSee('ข้อมูลเดิมยังอยู่ครบ');

        $this->actingAs($user)->post(route('properties.store'), $this->validPayload([
            'name' => 'รายการที่เกินลิมิต',
        ]))->assertRedirect(route('properties.create'))
            ->assertSessionHas('limit_reached', "แพ็กเกจฟรีเพิ่มทรัพย์ได้สูงสุด {$limit} รายการ ข้อมูลเดิมยังอยู่ครบ");

        $this->assertDatabaseCount('properties', $limit);
        $this->assertDatabaseHas('properties', ['id' => $properties->first()->id]);
    }

    public function test_guest_is_protected_from_property_routes(): void
    {
        $property = Property::factory()->create();

        foreach ([
            ['get', route('properties.index')],
            ['get', route('properties.create')],
            ['get', route('properties.show', $property)],
            ['get', route('properties.edit', $property)],
            ['post', route('properties.store')],
        ] as [$method, $url]) {
            $this->{$method}($url)->assertRedirect(route('login'));
        }
    }

    /** @param array<string, mixed> $overrides */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'transaction_type' => 'sale',
            'name' => 'สุขุมวิท เรสซิเดนซ์',
            'price' => 3500000,
            'bedrooms' => 2,
            'location' => 'สุขุมวิท',
        ], $overrides);
    }
}
