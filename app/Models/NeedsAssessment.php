<?php

namespace App\Models;

use Database\Factories\NeedsAssessmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NeedsAssessment extends Model
{
    /** @use HasFactory<NeedsAssessmentFactory> */
    use HasFactory;

    public const WEIGHTS = [
        'income_score' => 25,
        'vulnerability_score' => 20,
        'housing_score' => 15,
        'health_score' => 15,
        'education_score' => 10,
        'debts_score' => 10,
        'support_sources_score' => 5,
    ];

    public const LEVEL_LOW = 'low';

    public const LEVEL_MEDIUM = 'medium';

    public const LEVEL_HIGH = 'high';

    public const LEVEL_CRITICAL = 'critical';

    public const LEVEL_OPTIONS = [
        self::LEVEL_LOW => 'احتياج منخفض',
        self::LEVEL_MEDIUM => 'احتياج متوسط',
        self::LEVEL_HIGH => 'احتياج عال',
        self::LEVEL_CRITICAL => 'احتياج حرج',
    ];

    public const LEVEL_COLORS = [
        self::LEVEL_LOW => 'gray',
        self::LEVEL_MEDIUM => 'info',
        self::LEVEL_HIGH => 'warning',
        self::LEVEL_CRITICAL => 'danger',
    ];

    protected $fillable = [
        'case_study_id',
        'assessed_by_id',
        'income_score',
        'vulnerability_score',
        'housing_score',
        'health_score',
        'education_score',
        'debts_score',
        'support_sources_score',
        'total_score',
        'level',
        'notes',
        'assessed_at',
    ];

    protected function casts(): array
    {
        return [
            'income_score' => 'integer',
            'vulnerability_score' => 'integer',
            'housing_score' => 'integer',
            'health_score' => 'integer',
            'education_score' => 'integer',
            'debts_score' => 'integer',
            'support_sources_score' => 'integer',
            'total_score' => 'integer',
            'assessed_at' => 'datetime',
        ];
    }

    public function caseStudy(): BelongsTo
    {
        return $this->belongsTo(CaseStudy::class);
    }

    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by_id');
    }

    public function recalculate(): void
    {
        foreach (self::WEIGHTS as $attribute => $weight) {
            $this->{$attribute} = min(max((int) ($this->{$attribute} ?? 0), 0), $weight);
        }

        $this->total_score = collect(array_keys(self::WEIGHTS))
            ->sum(fn (string $attribute): int => (int) $this->{$attribute});

        $this->level = match (true) {
            $this->total_score <= 30 => self::LEVEL_LOW,
            $this->total_score <= 60 => self::LEVEL_MEDIUM,
            $this->total_score <= 80 => self::LEVEL_HIGH,
            default => self::LEVEL_CRITICAL,
        };
    }

    public static function levelLabelFor(?string $level): string
    {
        return self::LEVEL_OPTIONS[$level] ?? (string) $level;
    }

    public static function levelColorFor(?string $level): string
    {
        return self::LEVEL_COLORS[$level] ?? 'gray';
    }

    protected static function booted(): void
    {
        static::saving(function (NeedsAssessment $needsAssessment): void {
            $needsAssessment->recalculate();
        });
    }
}
