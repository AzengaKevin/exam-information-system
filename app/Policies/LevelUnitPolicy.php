<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LevelUnit;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class LevelUnitPolicy
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
        return $user->role->permissions->pluck('slug')->contains('level-units-browse')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to browse the classes page");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LevelUnit  $levelUnit
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, LevelUnit $levelUnit)
    {
        return $user->role->permissions->pluck('slug')->contains('level-units-read')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to the class, {$levelUnit->name}, details page");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('level-units-create')
            ? Response::allow()
            : Response::deny('Woops! You are not allowed to create a class');
        
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LevelUnit  $levelUnit
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, LevelUnit $levelUnit)
    {
        return $user->role->permissions->pluck('slug')->contains('level-units-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to update the class {$levelUnit->name}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LevelUnit  $levelUnit
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, LevelUnit $levelUnit)
    {
        return $user->role->permissions->pluck('slug')->contains('level-units-delete')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to delete the class {$levelUnit->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LevelUnit  $levelUnit
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, LevelUnit $levelUnit)
    {
        return $user->role->permissions->pluck('slug')->contains('level-units-restore')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to restore the class {$levelUnit->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\LevelUnit  $levelUnit
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, LevelUnit $levelUnit)
    {
        return $user->role->permissions->pluck('slug')->contains('level-units-destroy')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to completely delete the class {$levelUnit->name}");
    }

    /**
     * Determine whether a user is allowed to view trashed level units
     * 
     * @param User $user
     * @return Response
     */
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('level-units-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view trashed level units");
    }

    /**
     * Determine whether a user can bulk delete level units
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function bulkDelete(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('level-units-bulk-delete')
            ? Response::allow()
            : Response::deny('Woops! You are not allowed to bulk delete level units');
        
    }      
}
