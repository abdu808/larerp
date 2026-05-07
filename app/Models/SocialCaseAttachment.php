<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialCaseAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_case_id',
        'uploaded_by_id',
        'title',
        'file_path',
        'mime_type',
        'size',
        'notes',
    ];

    public function socialCase(): BelongsTo
    {
        return $this->belongsTo(SocialCase::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_id');
    }
}
