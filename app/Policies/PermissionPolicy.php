<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
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
        return $user->role->permissions->pluck('slug')->contains('permissions-browse')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to view the permissions list");
        
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Permission $permission)
    {
        return $user->role->permissions->pluck('slug')->contains('permissions-read')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to view the permission, {$permission->name}");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('permissions-create')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to create a permission");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Permission $permission)
    {
        return $user->role->permissions->pluck('slug')->contains('permissions-update')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to update the permission, {$permission->name}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Permission $permission)
    {
        return $user->role->permissions->pluck('slug')->contains('permissions-delete')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to delete the permission, {$permission->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Permission $permission)
    {
        return $user->role->permissions->pluck('slug')->contains('permissions-restore')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to restore the permission, {$permission->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Permission $permission)
    {
        return $user->role->permissions->pluck('slug')->contains('permissions-destroy')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to destroy the permission, {$permission->name}");
    }

    /**
     * Determine whether the user can view trashed permisions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */    
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('permissions-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to view trashed permissions");
        
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateLocked(User $user, Permission $permission)
    {
        return $user->role->permissions->pluck('slug')->contains('permissions-update-locked')
            ? Response::allow()
            : Response::deny("Can't update permission lock");
    }
    
}
