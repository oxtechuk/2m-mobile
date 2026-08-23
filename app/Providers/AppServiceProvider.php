<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);
        
        // Super-admin gate check
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return ($user->role === 'admin' || $user->hasRole('admin')) ? true : null;
        });
        
        // Load global helpers
        require_once app_path('Helpers/helpers.php');
    }
}
