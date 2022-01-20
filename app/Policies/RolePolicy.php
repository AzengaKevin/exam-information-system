<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RolePolicy
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
        return $user->role->permissions->pluck('slug')->contains('roles-browse')
            ? Response::allow()
            : Response::deny("You are not allowed to visit the roles page");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Role $role)
    {
        return $user->role->permissions->pluck('slug')->contains('roles-read')
            ? Response::allow()
            : Response::deny("You are not allowed to view the role, {$role->name}");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('roles-create')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to create a role");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Role $role)
    {
        return $user->role->permissions->pluck('slug')->contains('roles-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to update the role, {$role->name}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Role $role)
    {
        return $user->role->permissions->pluck('slug')->contains('roles-delete')
            ? Response::allow()
            : Response::deny("Woops! You're allowed to deleted the role, {$role->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Role $role)
    {
        return $user->role->permissions->pluck('slug')->contains('roles-restore')
            ? Response::allow()
            : Response::deny("Woops! You're allowed to restore the role, {$role->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Role $role)
    {
        return $user->role->permissions->pluck('slug')->contains('roles-destroy')
            ? Response::allow()
            : Response::deny("Woops! You're allowed to destroy the role, {$role->name}");
    }

    /**
     * Determine whether a user can manage roles permissions
     * 
     * @param User $user
     * @param Role $role
     * 
     * @return Response
     */
    public function managePermissions(User $user, Role $role)
    {
        return $user->role->permissions->pluck('slug')->contains('roles-manage-permissions')
            ? Response::allow()
            : Response::deny("You're not allowed to manage {$role->name} permissions");
        
    }

    /**
     * Determine whether the user can view trashed roles.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */    
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('roles-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to view trashed roles");
        
    }
}
