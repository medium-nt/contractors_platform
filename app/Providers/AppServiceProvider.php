<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Notification;

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

        Gate::define('is-expert', function (User $user) {
            return $user->role->name === 'expert' && $user->is_approved;
        });

        Gate::define('is-manager-or-admin', function (User $user) {
            return ($user->role->name === 'manager' || $user->role->name === 'admin') && $user->is_approved;
        });

        Gate::define('is-expert-or-admin', function (User $user) {
            return ($user->role->name === 'expert' || $user->role->name === 'admin') && $user->is_approved;
        });

        Gate::define('is-approved', function (User $user) {
            return $user->is_approved;
        });

        Gate::define('viewLogViewer', function (User $user) {
            return $user->isAdmin();
        });

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $notifications = Notification::query()
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->latest()->take(5)->get();

                $unreadCount = Notification::query()
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')->count();

                $view->with(compact('notifications', 'unreadCount'));
            }
        });

    }
}
