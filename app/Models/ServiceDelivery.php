<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceDelivery extends Model
{
    use HasFactory;

    public const TYPE_CASH = 'cash';

    public const TYPE_IN_KIND = 'in_kind';

    public const TYPE_SERVICE = 'service';

    public const TYPE_OPTIONS = [
        self::TYPE_CASH => 'صرف مالي',
        self::TYPE_IN_KIND => 'دعم عيني',
        self::TYPE_SERVICE => 'خدمة',
    ];

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_OPTIONS = [
        self::STATUS_SCHEDULED => 'مجدول',
        self::STATUS_IN_PROGRESS => 'قيد التنفيذ',
        self::STATUS_DELIVERED => 'منفذ',
        self::STATUS_FAILED => 'متعذر',
        self::STATUS_CANCELLED => 'ملغى',
    ];

    public const STATUS_COLORS = [
        self::STATUS_SCHEDULED => 'gray',
        self::STATUS_IN_PROGRESS => 'warning',
        self::STATUS_DELIVERED => 'success',
        self::STATUS_FAILED => 'danger',
        self::STATUS_CANCELLED => 'gray',
    ];

    protected $fillable = [
        'social_case_id',
        'support_plan_id',
        'committee_decision_id',
        'delivery_type',
        'status',
        'amount',
        'quantity',
        'unit',
        'service_description',
        'delivered_at',
        'delivered_by_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'quantity' => 'decimal:2',
            'delivered_at' => 'date',
        ];
    }

    public function socialCase(): BelongsTo
    {
        return $this->belongsTo(SocialCase::class);
    }

    public function supportPlan(): BelongsTo
    {
        return $this->belongsTo(SupportPlan::class);
    }

    public function committeeDecision(): BelongsTo
    {
        return $this->belongsTo(CommitteeDecision::class);
    }

    public function deliveredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivered_by_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
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
        static::saving(function (ServiceDelivery $delivery): void {
            if ($delivery->support_plan_id === null && $delivery->committee_decision_id === null) {
                throw new DomainException('Service delivery must be linked to a support plan or committee decision.');
            }

            if (
                $delivery->support_plan_id !== null
                && SupportPlan::query()
                    ->whereKey($delivery->support_plan_id)
                    ->where('social_case_id', '!=', $delivery->social_case_id)
                    ->exists()
            ) {
                throw new DomainException('Service delivery support plan must belong to the same social case.');
            }

            if (
                $delivery->committee_decision_id !== null
                && CommitteeDecision::query()
                    ->whereKey($delivery->committee_decision_id)
                    ->where('social_case_id', '!=', $delivery->social_case_id)
                    ->exists()
            ) {
                throw new DomainException('Service delivery committee decision must belong to the same social case.');
            }

            if ($delivery->amount !== null && (float) $delivery->amount <= 0) {
                throw new DomainException('Service delivery amount must be greater than zero when provided.');
            }

            if ($delivery->quantity !== null && (float) $delivery->quantity <= 0) {
                throw new DomainException('Service delivery quantity must be greater than zero when provided.');
            }
        });
    }
}
