<?php

namespace App\Models;

use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_ACTIVE => 'نشطة',
        self::STATUS_PAUSED => 'متوقفة مؤقتا',
        self::STATUS_COMPLETED => 'مكتملة',
    ];

    public const CHANNELS = [
        'website' => 'الموقع',
        'social' => 'الشبكات الاجتماعية',
        'branch' => 'الفرع',
        'manual' => 'يدوي',
    ];

    protected $fillable = [
        'project_id',
        'title',
        'code',
        'description',
        'status',
        'goal_amount',
        'collected_amount',
        'starts_at',
        'ends_at',
        'channel',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'goal_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Campaign $campaign): void {
            $errors = [];
            $campaign->status ??= self::STATUS_DRAFT;

            if (! array_key_exists((string) $campaign->status, self::STATUSES)) {
                $errors['status'][] = 'The selected campaign status is invalid.';
            }

            if ($campaign->channel && ! array_key_exists((string) $campaign->channel, self::CHANNELS)) {
                $errors['channel'][] = 'The selected campaign channel is invalid.';
            }

            if ((float) $campaign->goal_amount < 0) {
                $errors['goal_amount'][] = 'The campaign goal amount must be zero or greater.';
            }

            if ((float) $campaign->collected_amount < 0) {
                $errors['collected_amount'][] = 'The campaign collected amount must be zero or greater.';
            }

            if ((float) $campaign->goal_amount > 0 && (float) $campaign->collected_amount > (float) $campaign->goal_amount) {
                $errors['collected_amount'][] = 'The campaign collected amount may not exceed the goal amount.';
            }

            if ($campaign->starts_at && $campaign->ends_at && $campaign->ends_at->lt($campaign->starts_at)) {
                $errors['ends_at'][] = 'The campaign end date must be after or equal to the start date.';
            }

            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }
        });
    }

    public function getProgressPercentageAttribute(): int
    {
        if ((float) $this->goal_amount <= 0) {
            return 0;
        }

        return min(100, (int) round(((float) $this->collected_amount / (float) $this->goal_amount) * 100));
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
