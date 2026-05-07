<?php

namespace App\Providers;

use App\Models\OrganizationProfile;
use App\Models\User;
use App\Observers\AuditsModelChanges;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(AuditsModelChanges::class);
        OrganizationProfile::observe(AuditsModelChanges::class);
    }
}
