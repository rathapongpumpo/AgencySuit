<?php

namespace App\Models;

use Database\Factories\ClientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'transaction_type',
    'budget',
    'locations',
    'phone',
    'contact_channel',
    'bedrooms',
    'minimum_size',
    'transit_preference',
    'notes',
])]
class Client extends Model
{
    /** @use HasFactory<ClientFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'bedrooms' => 'integer',
            'minimum_size' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactionLabel(): string
    {
        return (string) config("clients.transaction_types.{$this->transaction_type}");
    }

    public function formattedBudget(): string
    {
        return number_format((float) $this->budget, 0).' บาท';
    }
}
