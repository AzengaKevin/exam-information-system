<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('change-exam-status', function(User $user){

            $isTeacher = $user->authenticatable_type == 'teacher';

            $isExamManager = false;

            /** @var Teacher */
            $teacher = $user->authenticatable;

            if($isTeacher) $isExamManager = $teacher->isExamManager();

            return $isExamManager
                ? Response::allow()
                : Response::deny('Only an Exam Manager can perform this action');

        });

        Gate::define('access-admin-dashboard', function(User $user){
            return $user->role->permissions->pluck('slug')->contains('dashboard-access')
                ? Response::allow()
                : Response::deny('You\'re not authorized to access the dashboard yet');
        });

        Gate::define('view-system-settings', function(User $user){
            return $user->role->permissions->pluck('slug')->contains('system-settings-view')
                ? Response::allow()
                : Response::deny('You\'re not authorized to view system settings');
        });

        Gate::define('view-general-settings', function(User $user){
            return $user->role->permissions->pluck('slug')->contains('general-settings-view')
                ? Response::allow()
                : Response::deny('You\'re not authorized to view general settings');
        });

        Gate::define('view-settings', function(User $user){
            return $user->role->permissions->pluck('slug')->contains('settings-view')
                ? Response::allow()
                : Response::deny('You\'re not authorized to view the settings page');
        });

        Gate::define('update-settings', function(User $user){
            return $user->role->permissions->pluck('slug')->contains('settings-update')
                ? Response::allow()
                : Response::deny('You\'re not authorized to update settings');
        });
    }
}
