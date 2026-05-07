<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_DRAFT => 'مسودة',
        self::STATUS_ACTIVE => 'نشط',
        self::STATUS_PAUSED => 'متوقف مؤقتا',
        self::STATUS_COMPLETED => 'مكتمل',
    ];

    protected $fillable = [
        'title',
        'code',
        'description',
        'category',
        'status',
        'goal_amount',
        'collected_amount',
        'starts_on',
        'ends_on',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'goal_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Project $project): void {
            $errors = [];
            $project->status ??= self::STATUS_DRAFT;

            if (! array_key_exists((string) $project->status, self::STATUSES)) {
                $errors['status'][] = 'The selected project status is invalid.';
            }

            if ((float) $project->goal_amount < 0) {
                $errors['goal_amount'][] = 'The project goal amount must be zero or greater.';
            }

            if ((float) $project->collected_amount < 0) {
                $errors['collected_amount'][] = 'The project collected amount must be zero or greater.';
            }

            if ((float) $project->goal_amount > 0 && (float) $project->collected_amount > (float) $project->goal_amount) {
                $errors['collected_amount'][] = 'The project collected amount may not exceed the goal amount.';
            }

            if ($project->starts_on && $project->ends_on && $project->ends_on->lt($project->starts_on)) {
                $errors['ends_on'][] = 'The project end date must be after or equal to the start date.';
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

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
