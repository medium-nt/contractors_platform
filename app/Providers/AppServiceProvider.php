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
            return $user->role->name === 'admin' && $user->is_approved;
        });

        Gate::define('is-manager', function (User $user) {
            return $user->role->name === 'manager' && $user->is_approved;
        });

        Gate::define('is-contractor', function (User $user) {
            return $user->role->name === 'contractor' && $user->is_approved;
        });

        Gate::define('is-manager-or-admin', function (User $user) {
            return ($user->role->name === 'manager' || $user->role->name === 'admin') && $user->is_approved;
        });

        Gate::define('is-contractor-or-admin', function (User $user) {
            return ($user->role->name === 'contractor' || $user->role->name === 'admin') && $user->is_approved;
        });

        Gate::define('is-approved', function (User $user) {
            return $user->is_approved;
        });
    }
}
