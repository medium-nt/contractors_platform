<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
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
        Paginator::useBootstrapFive();
        Paginator::useBootstrapFour();

        Gate::define('is-admin', function (User $user) {
            return $user->role->name === 'admin';
        });

        Gate::define('is-manager', function (User $user) {
            return $user->role->name === 'manager';
        });

        Gate::define('is-contractor', function (User $user) {
            return $user->role->name === 'contractor';
        });

        Gate::define('is-manager-or-admin', function (User $user) {
            return $user->role->name === 'manager' || $user->role->name === 'admin';
        });

        Gate::define('is-contractor-or-admin', function (User $user) {
            return $user->role->name === 'contractor' || $user->role->name === 'admin';
        });
    }
}
