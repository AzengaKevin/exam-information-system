<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use App\Settings\SystemSettings;
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
     * @return Response|bool
     */
    public function updateScoresTable(User $user, Exam $exam)
    {
        $isTeacher = $user->authenticatable_type == 'teacher';

        $isExamanager = false;

        /** @var Teacher */
        $teacher = $user->authenticatable;

        if($isTeacher) $isExamanager = $teacher->isExamManager();

        return $isExamanager && Schema::hasTable(Str::slug($exam->shortname))
            ? Response::allow()
            : Response::deny("Only nn Exam Manager can perform this action after, {$exam->name}, scores table is created");
    }

    /**
     * Determine whether a user is allowed to view scores page
     * 
     * @param Response
     */
    public function viewScoresPage(User $user, Exam $exam)
    {
        if(!$exam->isInMarking()) {
            return $exam->isPublished()
                ? Response::deny("The exam, {$exam->name}, has already been published, scores pages are out of bounds")
                : Response::deny("The exam, {$exam->name}, is not at marking stage yet, stay put");
        }

        $isTeacher = $user->authenticatable_type == 'teacher';

        $isExamManager = false;
        $isLsInCurrExam = false;
        $isCtInCurrExam = false;
        $isStInCurrExam = false;
    
        /** @var Teacher */
        $teacher = $user->authenticatable;

        if($isTeacher) $isExamManager = $teacher->isExamManager();

        if($isExamManager){

            return Response::allow();

        }else{

            $lsRes = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);
    
            if($isTeacher) $isLsInCurrExam = $teacher->responsibilities()
                ->wherePivotIn('level_id', $exam->levels->pluck('id')->all())
                ->get()
                ->contains($lsRes);

            if ($isLsInCurrExam) {

                return Response::allow();

            }else{

                $ctRes = Responsibility::firstOrCreate(['name' => 'Class Teacher']);
    
                /** @var SystemSettings */
                $systemSettings = app(SystemSettings::class);
        
                if ($systemSettings->school_has_streams) {
                    
                    if($isTeacher) $isCtInCurrExam = $teacher->responsibilities()
                        ->wherePivotIn('level_unit_id', $exam->getAllLevelUnits()->pluck('id')->all())
                        ->get()
                        ->contains($ctRes);
                }else{
                    
                    if($isTeacher) $isCtInCurrExam = $teacher->responsibilities()
                        ->wherePivotIn('level_id', $exam->levels->pluck('id')->all())
                        ->get()
                        ->contains($ctRes);
        
                }

                if($isCtInCurrExam){

                    return Response::allow();

                }else{

                    $stRes = Responsibility::firstOrCreate(['name' => 'Subject Teacher']);
        
                    if ($systemSettings->school_has_streams) {
                        
                        if($isTeacher) $isStInCurrExam = $teacher->responsibilities()
                            ->wherePivotIn('level_unit_id', $exam->getAllLevelUnits()->pluck('id')->all())
                            ->wherePivotIn('subject_id', $exam->subjects->pluck('id')->all())
                            ->get()
                            ->contains($stRes);
                    }else{
                        
                        if($isTeacher) $isStInCurrExam = $teacher->responsibilities()
                            ->wherePivotIn('level_id', $exam->levels->pluck('id')->all())
                            ->wherePivotIn('subject_id', $exam->subjects->pluck('id')->all())
                            ->get()
                            ->contains($stRes);
            
                    }

                    return $isStInCurrExam
                        ? Response::allow()
                        : Response::deny("You're not allowed to view the exam, {$exam->name}, scores page");
                }
            }
        }
    }

    /**
     * Determine whether a user can view exam transcripts at any given time
     * 
     * @return Response
     */
    public function viewTranscripts(User $user, Exam $exam)
    {

        if(!$exam->isPublished()) return Response::deny("The exam, {$exam->name}, is not published yet, stay put");

        $isTeacher = $user->authenticatable_type == 'teacher';

        $isExamManager = false;
        $isLsInCurrExam = false;
        $isCtInCurrExam = false;
    
        /** @var Teacher */
        $teacher = $user->authenticatable;
    
        if($isTeacher) $isExamManager = $teacher->isExamManager();

        if($isExamManager){

            return Response::allow();

        }else{

            $lsRes = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);
    
            if($isTeacher) $isLsInCurrExam = $teacher->responsibilities()
                ->wherePivotIn('level_id', $exam->levels->pluck('id')->all())
                ->get()
                ->contains($lsRes);

            if ($isLsInCurrExam) {

                return Response::allow();

            }else{

                $ctRes = Responsibility::firstOrCreate(['name' => 'Class Teacher']);
    
                /** @var SystemSettings */
                $systemSettings = app(SystemSettings::class);
        
                if ($systemSettings->school_has_streams) {
                    
                    if($isTeacher) $isCtInCurrExam = $teacher->responsibilities()
                        ->wherePivotIn('level_unit_id', $exam->getAllLevelUnits()->pluck('id')->all())
                        ->get()
                        ->contains($ctRes);
                }else{
                    
                    if($isTeacher) $isCtInCurrExam = $teacher->responsibilities()
                        ->wherePivotIn('level_id', $exam->levels()->pluck('id')->all())
                        ->get()
                        ->contains($ctRes);
        
                }
                
                return $isCtInCurrExam
                    ? Response::allow()
                    : Response::deny("You're not allowed to view the exam, {$exam->name}. transcripts page");
            }
        }
    }

    /**
     * Determine whether a user can view exam results at any given time
     * 
     * @return Response
     */
    public function viewResults(User $user, Exam $exam)
    {
        if(!$exam->isPublished()) return Response::deny("The exam, {$exam->name}, is not published yet, stay put");

        return Response::allow();

    }
}
