<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialCase extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_OPTIONS = [
        self::STATUS_OPEN => 'مفتوحة',
        self::STATUS_UNDER_REVIEW => 'قيد الدراسة',
        self::STATUS_APPROVED => 'معتمدة',
        self::STATUS_CLOSED => 'مغلقة',
    ];

    public const STATUS_COLORS = [
        self::STATUS_OPEN => 'info',
        self::STATUS_UNDER_REVIEW => 'warning',
        self::STATUS_APPROVED => 'success',
        self::STATUS_CLOSED => 'gray',
    ];

    public const PRIORITY_OPTIONS = [
        'low' => 'منخفضة',
        'normal' => 'عادية',
        'high' => 'عالية',
        'urgent' => 'عاجلة',
    ];

    public const PRIORITY_COLORS = [
        'low' => 'gray',
        'normal' => 'info',
        'high' => 'warning',
        'urgent' => 'danger',
    ];

    public const TYPE_OPTIONS = [
        'financial' => 'احتياج مالي',
        'housing' => 'سكن',
        'health' => 'صحي',
        'education' => 'تعليمي',
        'emergency' => 'طارئ',
    ];

    protected $fillable = [
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

    public function canBeDeleted(): bool
    {
        return ! $this->notes()->exists()
            && ! $this->attachments()->exists();
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public static function statusLabelFor(?string $status): string
    {
        return self::STATUS_OPTIONS[$status] ?? (string) $status;
    }

    public static function statusColorFor(?string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'gray';
    }

    public static function priorityLabelFor(?string $priority): string
    {
        return self::PRIORITY_OPTIONS[$priority] ?? (string) $priority;
    }

    public static function priorityColorFor(?string $priority): string
    {
        return self::PRIORITY_COLORS[$priority] ?? 'gray';
    }

    protected static function booted(): void
    {
        static::saving(function (SocialCase $socialCase): void {
            if ($socialCase->isClosed() && $socialCase->closed_at === null) {
                $socialCase->closed_at = now()->toDateString();
            }

            if (! $socialCase->isClosed()) {
                $socialCase->closed_at = null;
            }
        });

        static::deleting(function (SocialCase $socialCase): void {
            if (! $socialCase->canBeDeleted()) {
                throw new DomainException('Cannot delete a social case that has notes or attachments.');
            }
        });
    }
}
