<?php

namespace App\Models;

use App\Services\CommissionService;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'client_id', 'property_id', 'stage', 'amount', 'commission_rate', 'co_agent_split', 'closed_at'])]
class Deal extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'co_agent_split' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function stageLabel(): string
    {
        return (string) config("deals.stages.{$this->stage}");
    }

    public function grossCommission(): float
    {
        return app(CommissionService::class)->gross($this);
    }

    public function agentCommission(): float
    {
        return app(CommissionService::class)->agent($this);
    }
}
