<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Grading;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('gradings-browse')
            ? Response::allow()
            : Response::deny('You are not allowed to browse the Grading Systems page');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Grading  $grading
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Grading $grading)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Grading  $grading
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Grading $grading)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Grading  $grading
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Grading $grading)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Grading  $grading
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Grading $grading)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Grading  $grading
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Grading $grading)
    {
        //
    }
}
