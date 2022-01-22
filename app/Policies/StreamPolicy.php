<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Stream;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class StreamPolicy
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
        return $user->role->permissions->pluck('slug')->contains('streams-browse')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to browse the streams page");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stream  $stream
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Stream $stream)
    {
        return $user->role->permissions->pluck('slug')->contains('streams-read')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view stream, {$stream->name}, details");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('streams-create')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to create a stream");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stream  $stream
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Stream $stream)
    {
        return $user->role->permissions->pluck('slug')->contains('streams-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to update the stream, {$stream->name}");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stream  $stream
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Stream $stream)
    {
        return $user->role->permissions->pluck('slug')->contains('streams-delete')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to delete the stream, {$stream->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stream  $stream
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Stream $stream)
    {
        return $user->role->permissions->pluck('slug')->contains('streams-restore')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to restore the stream, {$stream->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stream  $stream
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Stream $stream)
    {
        return $user->role->permissions->pluck('slug')->contains('streams-destroy')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to completely delete the stream, {$stream->name}");
    }

    /**
     * Determine whether a user is allowed to view trashed streams
     * 
     * @param User $user
     * @return Response
     */
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('streams-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view trashed streams");
    }

    /**
     * Determine whether a user can bulk delete streams
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function bulkDelete(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('streams-bulk-delete')
            ? Response::allow()
            : Response::deny('Woops! You are not allowed to bulk delete streams');
        
    }    
}
