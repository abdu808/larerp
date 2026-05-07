<?php

namespace App\Models;

use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

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

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
