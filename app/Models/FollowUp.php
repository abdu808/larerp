<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_DONE = 'done';

    public const STATUS_OVERDUE = 'overdue';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_OPTIONS = [
        self::STATUS_SCHEDULED => 'مجدولة',
        self::STATUS_DONE => 'منجزة',
        self::STATUS_OVERDUE => 'متأخرة',
        self::STATUS_CANCELLED => 'ملغاة',
    ];

    public const STATUS_COLORS = [
        self::STATUS_SCHEDULED => 'gray',
        self::STATUS_DONE => 'success',
        self::STATUS_OVERDUE => 'danger',
        self::STATUS_CANCELLED => 'gray',
    ];

    public const IMPROVEMENT_OPTIONS = [
        'none' => 'لا يوجد تحسن',
        'limited' => 'تحسن محدود',
        'moderate' => 'تحسن متوسط',
        'significant' => 'تحسن واضح',
    ];

    public const DECISION_OPTIONS = [
        'continue_support' => 'استمرار الدعم',
        'adjust_plan' => 'تعديل الخطة',
        'schedule_follow_up' => 'جدولة متابعة',
        'close_case' => 'إغلاق الحالة',
        'reassess' => 'إعادة تقييم',
    ];

    protected $fillable = [
        'social_case_id',
        'support_plan_id',
        'service_delivery_id',
        'status',
        'followed_up_at',
        'outcome',
        'improvement_level',
        'follow_up_decision',
        'next_follow_up_at',
        'followed_by_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'followed_up_at' => 'date',
            'next_follow_up_at' => 'date',
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

    public function serviceDelivery(): BelongsTo
    {
        return $this->belongsTo(ServiceDelivery::class);
    }

    public function followedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_by_id');
    }

    public static function statusLabelFor(?string $status): string
    {
        return self::STATUS_OPTIONS[$status] ?? (string) $status;
    }

    public static function statusColorFor(?string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'gray';
    }
}
