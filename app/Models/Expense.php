<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class Expense extends Model
{
    public const STATUSES = [
        'draft' => 'مسودة',
        'approved' => 'معتمد',
        'paid' => 'مدفوع',
        'cancelled' => 'ملغي',
    ];

    public const PAYMENT_METHODS = [
        'cash' => 'نقدي',
        'bank_transfer' => 'تحويل بنكي',
        'card' => 'بطاقة',
        'other' => 'أخرى',
    ];

    protected $fillable = [
        'financial_account_id',
        'category',
        'payee',
        'amount',
        'expense_date',
        'payment_method',
        'reference_number',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Expense $expense): void {
            if ($expense->status === null || $expense->status === '') {
                $expense->status = 'paid';
            }

            if ((float) $expense->amount <= 0) {
                throw new InvalidArgumentException('Expense amount must be greater than zero.');
            }

            if (! array_key_exists((string) $expense->status, self::STATUSES)) {
                throw new InvalidArgumentException('Expense status is invalid.');
            }

            if ($expense->payment_method !== null && ! array_key_exists((string) $expense->payment_method, self::PAYMENT_METHODS)) {
                throw new InvalidArgumentException('Expense payment method is invalid.');
            }
        });
    }

    public function financialAccount(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getAmountSummaryAttribute(): string
    {
        return number_format((float) $this->amount, 2).' SAR';
    }
}
