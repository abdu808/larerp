<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportPlan extends Model
{
    use HasFactory;

    public const TYPE_RELIEF = 'relief';

    public const TYPE_DEVELOPMENT = 'development';

    public const TYPE_OPTIONS = [
        self::TYPE_RELIEF => 'إغاثية',
        self::TYPE_DEVELOPMENT => 'تنموية',
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_OPTIONS = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_ACTIVE => 'نشطة',
        self::STATUS_PAUSED => 'متوقفة مؤقتا',
        self::STATUS_COMPLETED => 'مكتملة',
        self::STATUS_CANCELLED => 'ملغاة',
    ];

    public const STATUS_COLORS = [
        self::STATUS_DRAFT => 'gray',
        self::STATUS_ACTIVE => 'success',
        self::STATUS_PAUSED => 'warning',
        self::STATUS_COMPLETED => 'info',
        self::STATUS_CANCELLED => 'danger',
    ];

    protected $fillable = [
        'social_case_id',
        'plan_type',
        'goal',
        'status',
        'owner_id',
        'start_date',
        'end_date',
        'success_criteria',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function socialCase(): BelongsTo
    {
        return $this->belongsTo(SocialCase::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function serviceDeliveries(): HasMany
    {
        return $this->hasMany(ServiceDelivery::class);
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
}
