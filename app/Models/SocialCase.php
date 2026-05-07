<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
        'beneficiary_id',
        'case_number',
        'type',
        'status',
        'priority',
        'opened_at',
        'closed_at',
        'summary',
        'needs',
        'monthly_income',
        'monthly_expenses',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'date',
            'closed_at' => 'date',
            'monthly_income' => 'decimal:2',
            'monthly_expenses' => 'decimal:2',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(SocialCaseNote::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SocialCaseAttachment::class);
    }
}
