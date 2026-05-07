<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommitteeDecision extends Model
{
    use HasFactory;

    public const TYPE_FINANCIAL = 'financial';

    public const TYPE_SERVICE = 'service';

    public const TYPE_MIXED = 'mixed';

    public const TYPE_REJECTION = 'rejection';

    public const TYPE_DEFERRAL = 'deferral';

    public const TYPE_OPTIONS = [
        self::TYPE_FINANCIAL => 'دعم مالي',
        self::TYPE_SERVICE => 'خدمة',
        self::TYPE_MIXED => 'دعم مالي وخدمة',
        self::TYPE_REJECTION => 'رفض',
        self::TYPE_DEFERRAL => 'تأجيل',
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_DEFERRED = 'deferred';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_APPROVED => 'معتمد',
        self::STATUS_REJECTED => 'مرفوض',
        self::STATUS_DEFERRED => 'مؤجل',
        self::STATUS_CANCELLED => 'ملغى',
    ];

    public const STATUS_COLORS = [
        self::STATUS_DRAFT => 'gray',
        self::STATUS_APPROVED => 'success',
        self::STATUS_REJECTED => 'danger',
        self::STATUS_DEFERRED => 'warning',
        self::STATUS_CANCELLED => 'gray',
    ];

    protected $fillable = [
        'social_case_id',
        'assistance_request_id',
        'decision_type',
        'status',
        'approved_amount',
        'approved_service_type',
        'effective_from',
        'effective_to',
        'reason',
        'decided_by_id',
    ];

    protected function casts(): array
    {
        return [
            'approved_amount' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function socialCase(): BelongsTo
    {
        return $this->belongsTo(SocialCase::class);
    }

    public function assistanceRequest(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by_id');
    }

    public function serviceDeliveries(): HasMany
    {
        return $this->hasMany(ServiceDelivery::class);
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public static function statusLabelFor(?string $status): string
    {
        return self::STATUS_OPTIONS[$status] ?? (string) $status;
    }

    public static function statusColorFor(?string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'gray';
    }

    protected static function booted(): void
    {
        static::saving(function (CommitteeDecision $decision): void {
            if ($decision->isApproved() && blank($decision->reason)) {
                throw new DomainException('Approved committee decisions must include a documented reason.');
            }

            if ($decision->approved_amount !== null && (float) $decision->approved_amount < 0) {
                throw new DomainException('Approved amount cannot be negative.');
            }
        });
    }
}
