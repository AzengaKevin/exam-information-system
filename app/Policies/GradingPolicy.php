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
            : Response::deny('Woops! You are not allowed to browse the Grading Systems page');
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
        return $user->role->permissions->pluck('slug')->contains('gradings-read')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view the grading system, {$grading->name}, details");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('gradings-create')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to create a grading system");
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
        return $user->role->permissions->pluck('slug')->contains('gradings-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to update the grading system, {$grading->name}");
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
        return $user->role->permissions->pluck('slug')->contains('gradings-delete')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to delete the grading system, {$grading->name}");
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
        return $user->role->permissions->pluck('slug')->contains('gradings-restore')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to restore the grading system, {$grading->name}");
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
        return $user->role->permissions->pluck('slug')->contains('gradings-destroy')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to completely delete the grading system, {$grading->name}");
    }

    /**
     * Determine whether a user is allowed to view trashed gradings
     * 
     * @param User $user
     * @return Response
     */
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('gradings-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view trashed gradings");
    }
}
