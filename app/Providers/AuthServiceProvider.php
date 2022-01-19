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
    }
}
