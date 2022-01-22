<?php

namespace App\Policies;

use App\Models\Level;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LevelPolicy
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
        return $user->role->permissions->pluck('slug')->contains('levels-browse')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to browse the classes page");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Level $level)
    {
        return $user->role->permissions->pluck('slug')->contains('levels-read')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view details of the class {$level->name}");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('levels-create')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to create a level");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Level $level)
    {
        return $user->role->permissions->pluck('slug')->contains('levels-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to update the class, {$level->name}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Level $level)
    {
        return $user->role->permissions->pluck('slug')->contains('levels-delete')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to delete the level, {$level->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Level $level)
    {
        return $user->role->permissions->pluck('slug')->contains('levels-restore')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to restore the level, {$level->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Level  $level
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Level $level)
    {
        return $user->role->permissions->pluck('slug')->contains('levels-destroy')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to permanently the level, {$level->name}");
    }

    /**
     * Determine whether a user allowed to view trashed levels
     * 
     * @param User $user
     * @return Response
     */
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('levels-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view trashed levels");
    }

    /**
     * Determine whether a user can bulk delete levels
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function bulkDelete(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('levels-bulk-delete')
            ? Response::allow()
            : Response::deny('Woops! You are not allowed to bulk delete levels');
        
    }
}
