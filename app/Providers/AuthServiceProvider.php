<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Responsibility;
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

            $isDos = false;

            /** @var Teacher */
            $teacher = $user->authenticatable;

            $responsibility = Responsibility::firstOrCreate(['name' => 'Director of Studies']);

            if($isTeacher) $isDos = $teacher->responsibilities->contains($responsibility);


            return $isDos
                ? Response::allow()
                : Response::deny('Only DOS can perform this action');

        });
    }
}
