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
        'status',
        'city',
        'district',
        'address',
        'registered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_primary_contact' => 'boolean',
            'registered_at' => 'date',
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
            && ! $this->dependents()->exists();
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

    protected static function booted(): void
    {
        static::deleting(function (Beneficiary $beneficiary): void {
            if (! $beneficiary->canBeDeleted()) {
                throw new DomainException('Cannot delete a beneficiary that has social cases.');
            }
        });
    }
}
