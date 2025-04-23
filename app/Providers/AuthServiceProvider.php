<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate::before(function ($user, $ability) {
        //     return $user->hasRole('admin') ? true : null;
        // });

        Gate::define('edit-child', function ($user) {
            return $user->hasPermissionTo('edit-child') || session('temp_can_edit');
        });
    }
}
