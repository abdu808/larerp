<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_OPTIONS = [
        self::STATUS_ACTIVE => 'نشطة',
        self::STATUS_INACTIVE => 'غير نشطة',
        self::STATUS_ARCHIVED => 'مؤرشفة',
    ];

    public const STATUS_COLORS = [
        self::STATUS_ACTIVE => 'success',
        self::STATUS_INACTIVE => 'warning',
        self::STATUS_ARCHIVED => 'gray',
    ];

    protected $fillable = [
        'code',
        'name',
        'guardian_name',
        'phone',
        'city',
        'district',
        'address',
        'status',
        'registered_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'date',
        ];
    }

    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class);
    }

    public function socialCases(): HasMany
    {
        return $this->hasMany(SocialCase::class);
    }

    public function canBeDeleted(): bool
    {
        return ! $this->beneficiaries()->exists()
            && ! $this->socialCases()->exists();
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
        static::deleting(function (Family $family): void {
            if (! $family->canBeDeleted()) {
                throw new DomainException('Cannot delete a beneficiary file that has beneficiaries or social cases.');
            }
        });
    }
}
