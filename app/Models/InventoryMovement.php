<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class InventoryMovement extends Model
{
    public const TYPES = [
        'in' => 'إدخال',
        'out' => 'إخراج',
        'adjustment' => 'تسوية',
    ];

    protected $fillable = [
        'inventory_item_id',
        'type',
        'quantity',
        'movement_date',
        'reference_type',
        'reference_number',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'movement_date' => 'date',
            'unit_cost' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (InventoryMovement $movement): void {
            if (! array_key_exists((string) $movement->type, self::TYPES)) {
                throw new InvalidArgumentException('Inventory movement type is invalid.');
            }

            if ((float) $movement->quantity <= 0) {
                throw new InvalidArgumentException('Inventory movement quantity must be greater than zero.');
            }

            if ($movement->unit_cost !== null && (float) $movement->unit_cost < 0) {
                throw new InvalidArgumentException('Inventory movement unit cost cannot be negative.');
            }
        });
    }

    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getSignedQuantityAttribute(): float
    {
        return $this->type === 'out'
            ? -1 * (float) $this->quantity
            : (float) $this->quantity;
    }
}
