<?php

namespace App\Providers;

use App\Models\FieldOffice;
use App\Models\User;
use App\Observers\FieldOfficeObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Models\CustomSanctumModel;
use Laravel\Sanctum\Sanctum;

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
        //enabling the custom sanctum model
        // Sanctum::usePersonalAccessTokenModel(CustomSanctumModel::class);

        Gate::define('viewPulse', function (User $user) {
            return $user->can('s_admin');
        });

        FieldOffice::observe(FieldOfficeObserver::class);
    }
}
