<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use InvalidArgumentException;

class FinancialAccount extends Model
{
    public const ACCOUNT_TYPES = [
        'cash' => 'نقدي',
        'bank' => 'بنكي',
        'custody' => 'عهدة',
        'other' => 'أخرى',
    ];

    protected $fillable = [
        'code',
        'name',
        'type',
        'opening_balance',
        'current_balance',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (FinancialAccount $account): void {
            if (! array_key_exists((string) $account->type, self::ACCOUNT_TYPES)) {
                throw new InvalidArgumentException('Financial account type is invalid.');
            }

            if ((float) $account->opening_balance < 0 || (float) $account->current_balance < 0) {
                throw new InvalidArgumentException('Financial account balances cannot be negative.');
            }
        });
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::ACCOUNT_TYPES[$this->type] ?? $this->type;
    }

    public function getBalanceSummaryAttribute(): string
    {
        return number_format((float) $this->current_balance, 2).' SAR';
    }
}
