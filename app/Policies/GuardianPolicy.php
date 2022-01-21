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
        return $user->role->permissions->pluck('slug')->contains('guardians-read')
            ? Response::allow()
            : Response::deny("You are not allowed to view the guardian details, {$guardian->auth->name}");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('guardians-create')
            ? Response::allow()
            : Response::deny('You are not allowed to create a guardian');
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
        return $user->role->permissions->pluck('slug')->contains('guardians-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to updae the guardian, {$guardian->auth->name}");
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
        return $user->role->permissions->pluck('slug')->contains('guardians-delete')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to delete the guardian, {$guardian->auth->name}");
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
        return $user->role->permissions->pluck('slug')->contains('guardians-restore')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to restore the guardian, {$guardian->auth->name}");
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
        return $user->role->permissions->pluck('slug')->contains('guardians-destroy')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to destroy the guardian, {$guardian->auth->name}");
    }

    /**
     * Determine whether the user can view trashed permisions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */    
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('guardians-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to view trashed guardians");
        
    }  
}
