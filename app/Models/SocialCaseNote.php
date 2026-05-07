<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialCaseNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_case_id',
        'user_id',
        'type',
        'note',
        'noted_at',
    ];

    protected function casts(): array
    {
        return [
            'noted_at' => 'datetime',
        ];
    }

    public function socialCase(): BelongsTo
    {
        return $this->belongsTo(SocialCase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
