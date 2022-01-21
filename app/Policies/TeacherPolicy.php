<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
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
        return $user->role->permissions->pluck('slug')->contains('teachers-browse')
            ? Response::allow()
            : Response::deny('You are not allowed to browse the teachers page');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Teacher $teacher)
    {
        return $user->role->permissions->pluck('slug')->contains('teachers-read')
            ? Response::allow()
            : Response::deny("You are not allowed to view teacher, {$teacher->auth->name}, details page");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('teachers-create')
            ? Response::allow()
            : Response::deny("You are not allowed to create a teacher");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Teacher $teacher)
    {
        return $user->role->permissions->pluck('slug')->contains('teachers-update')
            ? Response::allow()
            : Response::deny("You are not allowed to update teacher {$teacher->auth->name} details");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Teacher $teacher)
    {
        return $user->role->permissions->pluck('slug')->contains('teachers-delete')
            ? Response::allow()
            : Response::deny("You are not allowed to delete teacher {$teacher->auth->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Teacher $teacher)
    {
        return $user->role->permissions->pluck('slug')->contains('teachers-restore')
            ? Response::allow()
            : Response::deny("You are not allowed to restore teacher {$teacher->auth->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Teacher  $teacher
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Teacher $teacher)
    {
        return $user->role->permissions->pluck('slug')->contains('teachers-destroy')
            ? Response::allow()
            : Response::deny("You are not allowed to destroy teacher {$teacher->auth->name}");
    }

    /**
     * Browse, Assign and Revoke teachers responsibilities
     * 
     * @param User $user
     * @param Teacher $teacher
     * 
     */
    public function manageTeacherResponsibilities(User $user, Teacher $teacher)
    {
        return $user->role->permissions->pluck('slug')->contains('teachers-manage-responsibilities')
            ? Response::allow()
            : Response::deny('Woops you are not allowed to manage teachers responsibilities');
    }


    /**
     * Determine whether the user can view trashed permisions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */    
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('teachers-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You're not allowed to view trashed teachers");
        
    }    
}
