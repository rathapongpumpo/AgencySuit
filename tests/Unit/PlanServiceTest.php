<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\PlanService;
use Tests\TestCase;

class PlanServiceTest extends TestCase
{
    public function test_free_plan_limits_are_read_from_central_config(): void
    {
        $service = new PlanService;
        $user = new User(['plan' => 'free']);

        $this->assertSame(5, $service->limit($user, 'clients'));
        $this->assertTrue($service->reached($user, 'clients', 5));
    }

    public function test_pro_plan_is_configured_without_a_hard_coded_limit(): void
    {
        $service = new PlanService;
        $user = new User(['plan' => 'pro']);

        $this->assertNull($service->limit($user, 'active_deals'));
        $this->assertFalse($service->reached($user, 'active_deals', 100));
    }
}
