<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
        return $user->role->permissions->pluck('slug')->contains('users-browse')
            ? Response::allow()
            : Response::deny('You\'re not allowed to access the users page');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        return $user->role->permissions->pluck('slug')->contains('users-read')
            ? Response::allow()
            : Response::deny("You\'re not allowed to view the users, {$model->name}");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('users-create')
            ? Response::allow()
            : Response::deny("You\'re not allowed to create a user");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        return $user->role->permissions->pluck('slug')->contains('users-update')
            ? Response::allow()
            : Response::deny("You\'re not allowed to update the user, {$model->name}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        return $user->role->permissions->pluck('slug')->contains('users-delete')
            ? Response::allow()
            : Response::deny("You\'re not allowed to delete the user, {$model->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        return $user->role->permissions->pluck('slug')->contains('users-restore')
            ? Response::allow()
            : Response::deny("You\'re not allowed to delete the user, {$model->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->role->permissions->pluck('slug')->contains('users-destroy')
            ? Response::allow()
            : Response::deny("You\'re not allowed to delete the user, {$model->name}");
    }

    /**
     * Determine whether the user can view trashed permisions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */    
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('users-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to view trashed users");
        
    }

    /**
     * Determine whether the user can bulk update users
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */  
    public function bulkUpdate(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('users-bulk-update')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to bulk update users");
    }
}
