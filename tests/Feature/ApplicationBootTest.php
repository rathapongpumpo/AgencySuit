<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationBootTest extends TestCase
{
    public function test_the_application_boots_with_the_safe_test_database_configuration(): void
    {
        $this->assertSame('mysql', config('database.default'));
        $this->assertSame('127.0.0.1', config('database.connections.mysql.host'));
        $this->assertSame('agencysuit_test', config('database.connections.mysql.database'));
    }

    public function test_the_root_route_sends_guests_to_login(): void
    {
        $this->get('/')
            ->assertRedirect(route('today'));

        $this->get(route('today'))->assertRedirect(route('login'));
    }

    public function test_the_authenticated_layout_reserves_the_four_primary_mobile_destinations(): void
    {
        $this->view('layouts.app')
            ->assertSee('วันนี้')
            ->assertSee('ทรัพย์')
            ->assertSee('ลูกค้า')
            ->assertSee('เพิ่มเติม');
    }
}
