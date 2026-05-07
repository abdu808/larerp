<?php

namespace App\Models;

use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
