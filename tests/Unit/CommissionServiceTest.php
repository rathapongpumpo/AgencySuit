<?php

namespace Tests\Unit;

use App\Models\Deal;
use App\Services\CommissionService;
use Tests\TestCase;

class CommissionServiceTest extends TestCase
{
    public function test_commission_example_matches_product_rule(): void
    {
        $deal = new Deal(['amount' => 4_500_000, 'commission_rate' => 3, 'co_agent_split' => 50]);
        $service = new CommissionService;

        $this->assertSame(135_000.0, $service->gross($deal));
        $this->assertSame(67_500.0, $service->agent($deal));
    }
}
