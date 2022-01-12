<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
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
        return $user->role->permissions->pluck('slug')->contains('exams-browse')
            ? Response::allow()
            : Response::deny('You are not allowed to browse the exams page');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Exam $exam)
    {
        return $user->role->permissions->pluck('slug')->contains('exams-read')
            ? Response::allow()
            : Response::deny('You are not allowed to access exam page');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->role->permissions->pluck('slug')->contains('exams-create')
            ? Response::allow()
            : Response::deny('Sorry, you are not allowed to create an exam');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Exam $exam)
    {
        return $user->role->permissions->pluck('slug')->contains('exams-update')
            ? Response::allow()
            : Response::deny('You are not allowed to update an exam');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Exam $exam)
    {
        return $user->role->permissions->pluck('slug')->contains('exams-delete')
            ? Response::allow()
            : Response::deny('You are not allowed to delete an exam');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Exam $exam)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Exam $exam)
    {
        //
    }

    /**
     * Determine whether the user can update the exam scores table, necssarily if the clumns have changed
     * 
     * @param User $user
     * @param Exam $exam
     * 
     * @rerun Response|bool
     */
    public function updateScoresTable(User $user, Exam $exam)
    {
        $isTeacher = $user->authenticatable_type == 'teacher';

        $isDos = false;

        /** @var Teacher */
        $teacher = $user->authenticatable;

        $responsibility = Responsibility::firstOrCreate(['name' => 'Director of Studies']);

        if($isTeacher) $isDos = $teacher->responsibilities->contains($responsibility);

        return $isDos && Schema::hasTable(Str::slug($exam->shortname))
            ? Response::allow()
            : Response::deny('Only DOS can perform this action, after the table is already create');
    }
}
