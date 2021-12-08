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
            : Response::deny('You are not allowed to browse the hostels page');
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
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
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
        //
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
        //
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
        //
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
        //
    }
}
