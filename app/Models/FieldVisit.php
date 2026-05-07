<?php

namespace App\Models;

use Database\Factories\FieldVisitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldVisit extends Model
{
    /** @use HasFactory<FieldVisitFactory> */
    use HasFactory;

    public const STATUS_SCHEDULED = 'scheduled';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_NO_ANSWER = 'no_answer';

    public const STATUS_POSTPONED = 'postponed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_OPTIONS = [
        self::STATUS_SCHEDULED => 'مجدولة',
        self::STATUS_COMPLETED => 'مكتملة',
        self::STATUS_NO_ANSWER => 'لا يوجد رد',
        self::STATUS_POSTPONED => 'مؤجلة',
        self::STATUS_CANCELLED => 'ملغاة',
    ];

    public const STATUS_COLORS = [
        self::STATUS_SCHEDULED => 'info',
        self::STATUS_COMPLETED => 'success',
        self::STATUS_NO_ANSWER => 'warning',
        self::STATUS_POSTPONED => 'gray',
        self::STATUS_CANCELLED => 'danger',
    ];

    public const TYPE_FIELD = 'field';

    public const TYPE_PHONE = 'phone';

    public const TYPE_OFFICE = 'office';

    public const TYPE_PARTNER = 'partner';

    public const TYPE_OPTIONS = [
        self::TYPE_FIELD => 'ميدانية',
        self::TYPE_PHONE => 'هاتفية',
        self::TYPE_OFFICE => 'مكتبية',
        self::TYPE_PARTNER => 'شريك',
    ];

    protected $fillable = [
        'case_study_id',
        'social_case_id',
        'visitor_id',
        'status',
        'type',
        'scheduled_at',
        'completed_at',
        'location',
        'purpose',
        'findings',
        'next_action',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function caseStudy(): BelongsTo
    {
        return $this->belongsTo(CaseStudy::class);
    }

    public function socialCase(): BelongsTo
    {
        return $this->belongsTo(SocialCase::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }

    public static function statusLabelFor(?string $status): string
    {
        return self::STATUS_OPTIONS[$status] ?? (string) $status;
    }

    public static function statusColorFor(?string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'gray';
    }

    public static function typeLabelFor(?string $type): string
    {
        return self::TYPE_OPTIONS[$type] ?? (string) $type;
    }
}
