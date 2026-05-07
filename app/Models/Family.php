<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

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
}
