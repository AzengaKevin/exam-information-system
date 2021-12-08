<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Guardian;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuardianPolicy
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
        return $user->role->permissions->pluck('slug')->contains('guardians-browse')
            ? Response::allow()
            : Response::deny('You are not allowed to browse the guardians page');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Guardian $guardian)
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
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Guardian $guardian)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Guardian $guardian)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Guardian $guardian)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Guardian  $guardian
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Guardian $guardian)
    {
        //
    }
}
