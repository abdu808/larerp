<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiary extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_id',
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
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_primary_contact' => 'boolean',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function socialCases(): HasMany
    {
        return $this->hasMany(SocialCase::class);
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
}
