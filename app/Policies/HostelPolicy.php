<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Hostel;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostelPolicy
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
        return $user->role->permissions->pluck('slug')->contains('hostels-browse')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to browse the hostels page");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hostel  $hostel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Hostel $hostel)
    {
        return $user->role->permissions->pluck('slug')->contains('hostels-read')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view the hostel, {$hostel->name}, details");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('hostels-create')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to create a hostel");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hostel  $hostel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Hostel $hostel)
    {
        return $user->role->permissions->pluck('slug')->contains('hostels-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to update the hostel, {$hostel->name}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hostel  $hostel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Hostel $hostel)
    {
        return $user->role->permissions->pluck('slug')->contains('hostels-delete')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to delete the hostel, {$hostel->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hostel  $hostel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Hostel $hostel)
    {
        return $user->role->permissions->pluck('slug')->contains('hostels-restore')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to restore the hostel, {$hostel->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Hostel  $hostel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Hostel $hostel)
    {
        return $user->role->permissions->pluck('slug')->contains('hostels-destroy')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to completely delete the hostel, {$hostel->name}");
    }

    /**
     * Determine whether a user is allowed to view trashed hostels
     * 
     * @param User $user
     * @return Response
     */
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('hostels-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view trashed hostels");
    }
}
