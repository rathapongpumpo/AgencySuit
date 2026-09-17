<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileAppShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_any_main_shell_destination(): void
    {
        foreach (['today', 'properties.index', 'clients.index', 'more'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_authenticated_user_can_open_the_mobile_shell_destinations(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('today'))
            ->assertOk()
            ->assertSee('ยังไม่มีรายการต้องทำ')
            ->assertSee('เพิ่มรายการแรก')
            ->assertSee('เพิ่มทรัพย์')
            ->assertSee('เพิ่มลูกค้า')
            ->assertSee(route('clients.create'), false)
            ->assertSee('นัดดู')
            ->assertSee('ติดตาม');

        $this->actingAs($user)->get(route('properties.index'))
            ->assertOk()
            ->assertSee('ยังไม่มีทรัพย์ในระบบ')
            ->assertSee('เพิ่มทรัพย์');

        $this->actingAs($user)->get(route('clients.index'))
            ->assertOk()
            ->assertSee('ยังไม่มีลูกค้าในระบบ')
            ->assertSee('เพิ่มลูกค้า');

        $this->actingAs($user)->get(route('more'))
            ->assertOk()
            ->assertSee('บัญชีผู้ใช้')
            ->assertSee('ส่งความคิดเห็น')
            ->assertSee('ออกจากระบบ');
    }
}
