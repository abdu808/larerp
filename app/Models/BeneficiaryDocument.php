<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeneficiaryDocument extends Model
{
    use HasFactory;

    public const STATUS_UPLOADED = 'uploaded';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_REPLACED = 'replaced';

    public const VERIFICATION_STATUS_OPTIONS = [
        self::STATUS_UPLOADED => 'مرفوعة',
        self::STATUS_UNDER_REVIEW => 'تحت المراجعة',
        self::STATUS_VERIFIED => 'موثقة',
        self::STATUS_REJECTED => 'مرفوضة',
        self::STATUS_EXPIRED => 'منتهية',
        self::STATUS_REPLACED => 'مستبدلة',
    ];

    public const VERIFICATION_STATUS_COLORS = [
        self::STATUS_UPLOADED => 'gray',
        self::STATUS_UNDER_REVIEW => 'warning',
        self::STATUS_VERIFIED => 'success',
        self::STATUS_REJECTED => 'danger',
        self::STATUS_EXPIRED => 'danger',
        self::STATUS_REPLACED => 'gray',
    ];

    public const DOCUMENT_TYPE_OPTIONS = [
        'national_id' => 'هوية وطنية/إقامة',
        'family_card' => 'سجل الأسرة',
        'income_statement' => 'إثبات دخل',
        'rent_contract' => 'عقد إيجار',
        'medical_report' => 'تقرير طبي',
        'education_document' => 'وثيقة تعليمية',
        'other' => 'أخرى',
    ];

    public const SENSITIVITY_LEVEL_OPTIONS = [
        'public' => 'عام',
        'internal' => 'داخلي',
        'confidential' => 'سري',
        'highly_sensitive' => 'عالي الحساسية',
    ];

    public const SENSITIVITY_LEVEL_COLORS = [
        'public' => 'gray',
        'internal' => 'info',
        'confidential' => 'warning',
        'highly_sensitive' => 'danger',
    ];

    protected $fillable = [
        'family_id',
        'beneficiary_id',
        'social_case_id',
        'assistance_request_id',
        'uploaded_by_id',
        'verified_by_id',
        'title',
        'file_path',
        'mime_type',
        'size',
        'document_type',
        'sensitivity_level',
        'verification_status',
        'issued_on',
        'expires_on',
        'verified_at',
        'used_in_decision_at',
        'rejection_reason',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'expires_on' => 'date',
            'verified_at' => 'datetime',
            'used_in_decision_at' => 'datetime',
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

    public function socialCase(): BelongsTo
    {
        return $this->belongsTo(SocialCase::class);
    }

    public function assistanceRequest(): BelongsTo
    {
        return $this->belongsTo(AssistanceRequest::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_on !== null && $this->expires_on->isPast();
    }

    public function isUsedInDecision(): bool
    {
        return $this->used_in_decision_at !== null;
    }

    public function canBeDeleted(): bool
    {
        return ! $this->isUsedInDecision();
    }

    public static function verificationStatusLabelFor(?string $status): string
    {
        return self::VERIFICATION_STATUS_OPTIONS[$status] ?? (string) $status;
    }

    public static function verificationStatusColorFor(?string $status): string
    {
        return self::VERIFICATION_STATUS_COLORS[$status] ?? 'gray';
    }

    public static function sensitivityLevelLabelFor(?string $level): string
    {
        return self::SENSITIVITY_LEVEL_OPTIONS[$level] ?? (string) $level;
    }

    public static function sensitivityLevelColorFor(?string $level): string
    {
        return self::SENSITIVITY_LEVEL_COLORS[$level] ?? 'gray';
    }

    protected static function booted(): void
    {
        static::saving(function (BeneficiaryDocument $document): void {
            if ($document->isExpired() && $document->verification_status !== self::STATUS_REPLACED) {
                $document->verification_status = self::STATUS_EXPIRED;
            }
        });

        static::deleting(function (BeneficiaryDocument $document): void {
            if (! $document->canBeDeleted()) {
                throw new DomainException('Cannot delete a beneficiary document that was used in a decision.');
            }
        });
    }
}
