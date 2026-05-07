<?php

namespace App\Models;

use Database\Factories\OrganizationProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationProfile extends Model
{
    /** @use HasFactory<OrganizationProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'legal_name',
        'charity_type',
        'license_number',
        'email',
        'phone',
        'website_url',
        'logo_path',
        'primary_color',
        'secondary_color',
        'settings',
        'active_modules',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'active_modules' => 'array',
        ];
    }
}
