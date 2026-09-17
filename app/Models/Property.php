<?php

namespace App\Models;

use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['user_id', 'transaction_type', 'name', 'price', 'bedrooms', 'location', 'status'])]
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory;

    public const DEFAULT_STATUS = 'available';

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'bedrooms' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)
            ->orderByDesc('is_primary')
            ->orderBy('id');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(PropertyPhoto::class)
            ->where('is_primary', true)
            ->orderBy('id');
    }

    protected function transactionLabel(): Attribute
    {
        return Attribute::get(fn (): string => (string) config("properties.transaction_types.{$this->transaction_type}"));
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(function (): string {
            $label = config("properties.statuses.{$this->status}");

            if (is_array($label)) {
                return (string) ($label[$this->transaction_type] ?? reset($label));
            }

            return (string) $label;
        });
    }

    public function formattedPrice(): string
    {
        return number_format((float) $this->price, 0).' บาท';
    }
}
