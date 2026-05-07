<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class InventoryItem extends Model
{
    protected $fillable = [
        'sku',
        'name',
        'category',
        'unit',
        'minimum_quantity',
        'current_quantity',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'minimum_quantity' => 'decimal:2',
            'current_quantity' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (InventoryItem $item): void {
            if ((float) $item->minimum_quantity < 0 || (float) $item->current_quantity < 0) {
                throw new InvalidArgumentException('Inventory quantities cannot be negative.');
            }
        });
    }

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getNeedsRestockAttribute(): bool
    {
        return (float) $this->current_quantity <= (float) $this->minimum_quantity;
    }

    public function getQuantitySummaryAttribute(): string
    {
        return number_format((float) $this->current_quantity, 2).' '.$this->unit;
    }
}
