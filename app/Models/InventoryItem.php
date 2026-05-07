<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}
