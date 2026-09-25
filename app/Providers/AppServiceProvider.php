<?php

namespace App\Providers;

use App\Contracts\GeocodingProviderInterface;
use App\Services\DisabledGeocodingProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GeocodingProviderInterface::class, DisabledGeocodingProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Gate::policy(\App\Models\Pointage::class, \App\Policies\PointagePolicy::class);
        Gate::policy(\App\Models\QrToken::class, \App\Policies\QrTokenPolicy::class);
        Gate::policy(\App\Models\Justificatif::class, \App\Policies\JustificatifPolicy::class);
        Gate::policy(\App\Models\Rapport::class, \App\Policies\RapportPolicy::class);
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
    }
}
