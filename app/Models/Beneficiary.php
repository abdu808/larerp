<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiary extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_ARCHIVED = 'archived';

    public const CLASSIFICATION_A = 'a';

    public const CLASSIFICATION_B = 'b';

    public const CLASSIFICATION_C = 'c';

    public const CLASSIFICATION_D = 'd';

    public const CLASSIFICATION_EXCLUDED = 'x';

    public const STATUS_OPTIONS = [
        self::STATUS_ACTIVE => 'نشط',
        self::STATUS_INACTIVE => 'غير نشط',
        self::STATUS_ARCHIVED => 'مؤرشف',
    ];

    public const STATUS_COLORS = [
        self::STATUS_ACTIVE => 'success',
        self::STATUS_INACTIVE => 'warning',
        self::STATUS_ARCHIVED => 'gray',
    ];

    public const GENDER_OPTIONS = [
        'male' => 'ذكر',
        'female' => 'أنثى',
    ];

    public const MARITAL_STATUS_OPTIONS = [
        'single' => 'أعزب/عزباء',
        'married' => 'متزوج/ة',
        'divorced' => 'مطلق/ة',
        'widowed' => 'أرمل/ة',
    ];

    protected $fillable = [
        'file_owner_id',
        'file_number',
        'national_id',
        'first_name',
        'father_name',
        'grandfather_name',
        'family_name',
        'gender',
        'birth_date',
        'phone',
        'relationship_to_guardian',
        'marital_status',
        'education_level',
        'employment_status',
        'health_status',
        'is_primary_contact',
        'nationality',
        'housing_type',
        'location_url',
        'bank_name',
        'iban',
        'classification',
        'score',
        'salary_income',
        'social_security_income',
        'citizen_account_income',
        'retirement_income',
        'other_income',
        'rent_expense',
        'electricity_expense',
        'water_expense',
        'loans_expense',
        'treatment_expense',
        'status',
        'status_reason',
        'city',
        'district',
        'address',
        'registered_at',
        'expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_primary_contact' => 'boolean',
            'registered_at' => 'date',
            'expiry_date' => 'date',
            'score' => 'integer',
            'salary_income' => 'decimal:2',
            'social_security_income' => 'decimal:2',
            'citizen_account_income' => 'decimal:2',
            'retirement_income' => 'decimal:2',
            'other_income' => 'decimal:2',
            'rent_expense' => 'decimal:2',
            'electricity_expense' => 'decimal:2',
            'water_expense' => 'decimal:2',
            'loans_expense' => 'decimal:2',
            'treatment_expense' => 'decimal:2',
        ];
    }

    public function fileOwner(): BelongsTo
    {
        return $this->belongsTo(self::class, 'file_owner_id');
    }

    public function dependents(): HasMany
    {
        return $this->hasMany(self::class, 'file_owner_id');
    }

    public function socialCases(): HasMany
    {
        return $this->hasMany(SocialCase::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BeneficiaryDocument::class);
    }

    public function assistanceRequests(): HasMany
    {
        return $this->hasMany(AssistanceRequest::class);
    }

    public function fieldVisits(): HasMany
    {
        return $this->hasMany(FieldVisit::class);
    }

    public function fileMembers(): HasMany
    {
        return $this->dependents();
    }

    public function getFullNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->father_name,
            $this->grandfather_name,
            $this->family_name,
        ])->filter()->implode(' ');
    }

    public function canBeDeleted(): bool
    {
        return ! $this->socialCases()->exists()
            && ! $this->documents()->exists()
            && ! $this->assistanceRequests()->exists()
            && ! $this->fieldVisits()->exists()
            && ! $this->dependents()->exists();
    }

    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->salary_income
            + (float) $this->social_security_income
            + (float) $this->citizen_account_income
            + (float) $this->retirement_income
            + (float) $this->other_income;
    }

    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->rent_expense
            + (float) $this->electricity_expense
            + (float) $this->water_expense
            + (float) $this->loans_expense
            + (float) $this->treatment_expense;
    }

    public function getDependentsCountForScoringAttribute(): int
    {
        return $this->relationLoaded('dependents') ? $this->dependents->count() : $this->dependents()->count();
    }

    public function getFileMembersCountAttribute(): int
    {
        return $this->dependents_count_for_scoring + 1;
    }

    public static function genderLabelFor(?string $gender): string
    {
        return self::GENDER_OPTIONS[$gender] ?? (string) $gender;
    }

    public static function statusLabelFor(?string $status): string
    {
        return self::STATUS_OPTIONS[$status] ?? (string) $status;
    }

    public static function statusColorFor(?string $status): string
    {
        return self::STATUS_COLORS[$status] ?? 'gray';
    }

    public static function classificationLabelFor(?string $classification): string
    {
        return [
            self::CLASSIFICATION_A => 'A - احتياج عال',
            self::CLASSIFICATION_B => 'B - احتياج متوسط',
            self::CLASSIFICATION_C => 'C - احتياج منخفض',
            self::CLASSIFICATION_D => 'D - غير مستحق حاليا',
            self::CLASSIFICATION_EXCLUDED => 'X - مستبعد',
        ][$classification] ?? (string) $classification;
    }

    public static function classificationColorFor(?string $classification): string
    {
        return [
            self::CLASSIFICATION_A => 'danger',
            self::CLASSIFICATION_B => 'warning',
            self::CLASSIFICATION_C => 'info',
            self::CLASSIFICATION_D => 'gray',
            self::CLASSIFICATION_EXCLUDED => 'gray',
        ][$classification] ?? 'gray';
    }

    public function refreshSocialClassification(): void
    {
        $membersCount = max(1, $this->file_members_count);
        $perCapita = $this->total_income > 0 ? max(0, $this->total_income - $this->rent_expense - $this->treatment_expense) / $membersCount : 0;
        $score = 0;

        if ($this->total_income <= 0) {
            $score += 40;
        } elseif ($perCapita < 300) {
            $score += 35;
        } elseif ($perCapita < 600) {
            $score += 25;
        } elseif ($perCapita < 1000) {
            $score += 10;
        }

        if (in_array($this->marital_status, ['widowed', 'divorced'], true)) {
            $score += $this->marital_status === 'widowed' ? 25 : 20;
        }

        if ($membersCount >= 7) {
            $score += 20;
        } elseif ($membersCount >= 4) {
            $score += 10;
        }

        if (in_array($this->housing_type, ['rented', 'hosted', 'heir'], true)) {
            $score += $this->housing_type === 'rented' ? 20 : 10;
        }

        if ($this->health_status && ! in_array($this->health_status, ['سليم', 'healthy'], true)) {
            $score += 15;
        }

        $this->score = min(100, $score);
        $this->classification = match (true) {
            $this->score >= 80 => self::CLASSIFICATION_A,
            $this->score >= 60 => self::CLASSIFICATION_B,
            $this->score >= 35 => self::CLASSIFICATION_C,
            default => self::CLASSIFICATION_D,
        };
    }

    protected static function booted(): void
    {
        static::saving(function (Beneficiary $beneficiary): void {
            if ($beneficiary->file_number === null && $beneficiary->file_owner_id === null) {
                $nextId = ((int) static::query()->max('id')) + 1;
                $beneficiary->file_number = 'BEN-'.str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
            }

            if ($beneficiary->file_owner_id === null) {
                $beneficiary->refreshSocialClassification();
            }
        });

        static::deleting(function (Beneficiary $beneficiary): void {
            if (! $beneficiary->canBeDeleted()) {
                throw new DomainException('Cannot delete a beneficiary that has social cases.');
            }
        });
    }
}
