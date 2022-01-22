<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class StudentPolicy
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
        return $user->role->permissions->pluck('slug')->contains('students-browse')
            ? Response::allow()
            : Response::deny('Woops! You are not allowed to browse the students page');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Student $student)
    {
        return $user->role->permissions->pluck('slug')->contains('students-read')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view student, {$student->name}, details page");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('students-create')
            ? Response::allow()
            : Response::deny('Woops! You are not allowed to create a student');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Student $student)
    {
        return $user->role->permissions->pluck('slug')->contains('students-update')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to update the student, {$student->name}, details");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Student $student)
    {
        return $user->role->permissions->pluck('slug')->contains('students-delete')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to delete the student, {$student->name}");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Student $student)
    {
        return $user->role->permissions->pluck('slug')->contains('students-restore')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to restore the student, {$student->name}");
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Student $student)
    {
        return $user->role->permissions->pluck('slug')->contains('students-destroy')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to completely delete the student, {$student->name}, from the application");
    }

    /**
     * Determine whether a user allowed to view trashed students
     */
    public function viewTrashed(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('students-view-trashed')
            ? Response::allow()
            : Response::deny("Woops! You are not allowed to view trashed students");
    }
}
