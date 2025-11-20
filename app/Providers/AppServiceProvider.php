<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

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
         Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

             // Set token expiration
        Passport::tokensExpireIn(now()->addDays(30));
        Passport::refreshTokensExpireIn(now()->addDays(60));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        
        // Define token scopes - THIS IS IMPORTANT
        Passport::tokensCan([
            'customer' => 'Customer access',
            'user' => 'User access',
            'location-admin' => 'Location admin access',
            'franchise-admin' => 'Franchise admin access',
        ]);
        
        // Set default scope (optional)
        Passport::setDefaultScope([
            'customer',
            'user',
            'location-admin',
            'franchise-admin',
        ]);
    }
}
