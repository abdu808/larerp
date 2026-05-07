<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssistanceRequest extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_SCREENING = 'screening';

    public const STATUS_NEEDS_INFORMATION = 'needs_information';

    public const STATUS_REJECTED_INITIALLY = 'rejected_initially';

    public const STATUS_ASSIGNED_TO_RESEARCHER = 'assigned_to_researcher';

    public const STATUS_UNDER_STUDY = 'under_study';

    public const STATUS_PENDING_SUPERVISOR_REVIEW = 'pending_supervisor_review';

    public const STATUS_PENDING_COMMITTEE = 'pending_committee';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_APPROVED_WITH_CHANGES = 'approved_with_changes';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_PENDING_DELIVERY = 'pending_delivery';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_UNDER_FOLLOW_UP = 'under_follow_up';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_SUBMITTED => 'مقدم',
        self::STATUS_SCREENING => 'تحت الفرز',
        self::STATUS_NEEDS_INFORMATION => 'يحتاج استكمال',
        self::STATUS_REJECTED_INITIALLY => 'مرفوض أوليا',
        self::STATUS_ASSIGNED_TO_RESEARCHER => 'محال لباحث',
        self::STATUS_UNDER_STUDY => 'تحت الدراسة',
        self::STATUS_PENDING_SUPERVISOR_REVIEW => 'بانتظار مراجعة المشرف',
        self::STATUS_PENDING_COMMITTEE => 'بانتظار اللجنة',
        self::STATUS_APPROVED => 'معتمد',
        self::STATUS_APPROVED_WITH_CHANGES => 'معتمد بتعديل',
        self::STATUS_REJECTED => 'مرفوض',
        self::STATUS_PENDING_DELIVERY => 'بانتظار التنفيذ',
        self::STATUS_DELIVERED => 'منفذ',
        self::STATUS_UNDER_FOLLOW_UP => 'تحت المتابعة',
        self::STATUS_CLOSED => 'مغلق',
        self::STATUS_CANCELLED => 'ملغى',
    ];

    public const STATUS_COLORS = [
        self::STATUS_DRAFT => 'gray',
        self::STATUS_SUBMITTED => 'info',
        self::STATUS_SCREENING => 'warning',
        self::STATUS_NEEDS_INFORMATION => 'warning',
        self::STATUS_REJECTED_INITIALLY => 'danger',
        self::STATUS_ASSIGNED_TO_RESEARCHER => 'info',
        self::STATUS_UNDER_STUDY => 'warning',
        self::STATUS_PENDING_SUPERVISOR_REVIEW => 'warning',
        self::STATUS_PENDING_COMMITTEE => 'warning',
        self::STATUS_APPROVED => 'success',
        self::STATUS_APPROVED_WITH_CHANGES => 'success',
        self::STATUS_REJECTED => 'danger',
        self::STATUS_PENDING_DELIVERY => 'info',
        self::STATUS_DELIVERED => 'success',
        self::STATUS_UNDER_FOLLOW_UP => 'info',
        self::STATUS_CLOSED => 'gray',
        self::STATUS_CANCELLED => 'gray',
    ];

    public const TYPE_OPTIONS = [
        'financial' => 'دعم مالي',
        'housing' => 'سكن',
        'health' => 'صحي',
        'education' => 'تعليمي',
        'emergency' => 'طارئ',
        'other' => 'أخرى',
    ];

    public const URGENCY_OPTIONS = [
        'low' => 'منخفضة',
        'normal' => 'عادية',
        'high' => 'عالية',
        'urgent' => 'عاجلة',
    ];

    public const URGENCY_COLORS = [
        'low' => 'gray',
        'normal' => 'info',
        'high' => 'warning',
        'urgent' => 'danger',
    ];

    protected $fillable = [
        'beneficiary_id',
        'social_case_id',
        'assigned_to_id',
        'request_number',
        'request_type',
        'status',
        'urgency',
        'source',
        'submitted_at',
        'description',
        'consent_to_store_data',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'consent_to_store_data' => 'boolean',
        ];
    }

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function socialCase(): BelongsTo
    {
        return $this->belongsTo(SocialCase::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BeneficiaryDocument::class);
    }

    public function isSubmitted(): bool
    {
        return $this->status !== self::STATUS_DRAFT;
    }

    public function isClosed(): bool
    {
        return in_array($this->status, [self::STATUS_CLOSED, self::STATUS_CANCELLED, self::STATUS_REJECTED], true);
    }

    public static function statusLabelFor(?string $status): string
    {
        return self::STATUS_OPTIONS[$status] ?? (string) $status;
    }

    public static function statusColorFor(?string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'gray';
    }

    public static function urgencyLabelFor(?string $urgency): string
    {
        return self::URGENCY_OPTIONS[$urgency] ?? (string) $urgency;
    }

    public static function urgencyColorFor(?string $urgency): string
    {
        return self::URGENCY_COLORS[$urgency] ?? 'gray';
    }
}
