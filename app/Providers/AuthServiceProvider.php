<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Permission::class => \App\Policies\PermissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define gates for permissions
        Gate::define('system.manage', function (User $user) {
            return $user->hasPermission('system.manage');
        });

        Gate::define('users.manage', function (User $user) {
            return $user->hasPermission('users.manage');
        });

        Gate::define('users.view', function (User $user) {
            return $user->hasPermission('users.view') || $user->hasPermission('users.manage');
        });

        Gate::define('roles.manage', function (User $user) {
            return $user->hasPermission('roles.manage');
        });

        Gate::define('permissions.manage', function (User $user) {
            return $user->hasPermission('permissions.manage');
        });

        Gate::define('settings.manage', function (User $user) {
            return $user->hasPermission('settings.manage');
        });

        Gate::define('students.manage', function (User $user) {
            return $user->hasPermission('students.manage');
        });

        Gate::define('students.view', function (User $user) {
            return $user->hasPermission('students.view') || $user->hasPermission('students.manage');
        });

        Gate::define('grades.manage', function (User $user) {
            return $user->hasPermission('grades.manage');
        });

        Gate::define('grades.view', function (User $user) {
            return $user->hasPermission('grades.view') || $user->hasPermission('grades.manage');
        });

        Gate::define('classes.manage', function (User $user) {
            return $user->hasPermission('classes.manage');
        });

        Gate::define('curriculum.manage', function (User $user) {
            return $user->hasPermission('curriculum.manage');
        });

        Gate::define('academic.manage', function (User $user) {
            return $user->hasPermission('academic.manage');
        });

        Gate::define('policies.manage', function (User $user) {
            return $user->hasPermission('policies.manage');
        });

        Gate::define('enrollment.manage', function (User $user) {
            return $user->hasPermission('enrollment.manage');
        });

        Gate::define('admission.manage', function (User $user) {
            return $user->hasPermission('admission.manage');
        });

        Gate::define('finance.manage', function (User $user) {
            return $user->hasPermission('finance.manage');
        });

        Gate::define('finance.view', function (User $user) {
            return $user->hasPermission('finance.view') || $user->hasPermission('finance.manage');
        });

        Gate::define('reports.view', function (User $user) {
            return $user->hasPermission('reports.view');
        });

        Gate::define('reports.generate', function (User $user) {
            return $user->hasPermission('reports.generate');
        });

        // Super admin bypass - has all permissions
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('super_admin')) {
                return true;
            }
        });
    }
}