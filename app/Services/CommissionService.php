<?php

namespace App\Services;

use App\Models\Deal;

class CommissionService
{
    public function gross(Deal $deal): float
    {
        return round(((float) $deal->amount * (float) $deal->commission_rate) / 100, 2);
    }

    public function agent(Deal $deal): float
    {
        return round($this->gross($deal) * (1 - ((float) $deal->co_agent_split / 100)), 2);
    }
}
