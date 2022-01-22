<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Department;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
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
        return $user->role->permissions->pluck('slug')->contains('departments-browse')
            ? Response::allow()
            : Response::deny('You are not allowed to browse the departments page');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Department $department)
    {
        return $user->role->permissions->pluck('slug')->contains('departments-read')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view the department, {$department->name}, details page");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('departments-create')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to create a department");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Department $department)
    {
        return $user->role->permissions->pluck('slug')->contains('departments-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to update the department, {$department->name}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Department $department)
    {
        return $user->role->permissions->pluck('slug')->contains('departments-delete')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to delete the department, {$department->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Department $department)
    {
        return $user->role->permissions->pluck('slug')->contains('departments-restore')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to restore the department, {$department->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Department $department)
    {
        return $user->role->permissions->pluck('slug')->contains('departments-destroy')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to completely delete the department, {$department->name}");
    }

    /**
     * Determine whether the user can view trashed departments.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */    
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('departments-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to view trashed departments");
        
    }     
}
