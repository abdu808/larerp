<?php

namespace App\Models;

use Database\Factories\CaseStudyFactory;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CaseStudy extends Model
{
    /** @use HasFactory<CaseStudyFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_UNDER_STUDY = 'under_study';

    public const STATUS_READY_FOR_SUPERVISOR = 'ready_for_supervisor';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_UNDER_STUDY => 'تحت الدراسة',
        self::STATUS_READY_FOR_SUPERVISOR => 'جاهزة للمشرف',
        self::STATUS_COMPLETED => 'مكتملة',
    ];

    public const STATUS_COLORS = [
        self::STATUS_DRAFT => 'gray',
        self::STATUS_UNDER_STUDY => 'warning',
        self::STATUS_READY_FOR_SUPERVISOR => 'info',
        self::STATUS_COMPLETED => 'success',
    ];

    protected $fillable = [
        'social_case_id',
        'assistance_request_id',
        'researcher_id',
        'supervisor_id',
        'status',
        'started_at',
        'ready_for_supervisor_at',
        'completed_at',
        'summary',
        'family_situation',
        'risk_factors',
        'recommendation',
        'supervisor_notes',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'date',
            'ready_for_supervisor_at' => 'datetime',
            'completed_at' => 'datetime',
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

    public function researcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'researcher_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function needsAssessment(): HasOne
    {
        return $this->hasOne(NeedsAssessment::class);
    }

    public function fieldVisits(): HasMany
    {
        return $this->hasMany(FieldVisit::class);
    }

    public function canBeSubmitted(): bool
    {
        return filled($this->recommendation) && $this->needsAssessment()->exists();
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
        static::saving(function (CaseStudy $caseStudy): void {
            if (in_array($caseStudy->status, [self::STATUS_READY_FOR_SUPERVISOR, self::STATUS_COMPLETED], true)
                && ! $caseStudy->canBeSubmitted()) {
                throw new DomainException('Case study cannot be completed or sent to supervisor without an assessment and recommendation.');
            }

            if ($caseStudy->status === self::STATUS_READY_FOR_SUPERVISOR && $caseStudy->ready_for_supervisor_at === null) {
                $caseStudy->ready_for_supervisor_at = now();
            }

            if ($caseStudy->status === self::STATUS_COMPLETED && $caseStudy->completed_at === null) {
                $caseStudy->completed_at = now();
            }
        });
    }
}
