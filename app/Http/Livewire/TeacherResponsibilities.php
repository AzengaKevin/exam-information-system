<?php

namespace App\Http\Livewire;

use App\Models\Level;
use App\Models\Teacher;
use Livewire\Component;
use App\Models\LevelUnit;
use App\Models\Department;
use App\Models\Responsibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Models\ResponsibilityTeacher;
use App\Settings\SystemSettings;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeacherResponsibilities extends Component
{
    use AuthorizesRequests;

    public Teacher $teacher;

    public $allResponsibilities;
    public $levels;
    public $subjects;
    public $departments;
    public $levelUnits;

    public $responsibility_id;
    public $level_unit_id;
    public $level_id;
    public $department_id;
    public $subject_id;

    /** @var array required field resonsibilities */
    public $fields = [];

    public $type = null;

    public $selectAllClasses;

    public $teacher_subject_id;

    public $selectedClasses = [];

    public $allLevelUnitsMissingTeacherForThatSubject;

    public $allLevelsMissingTeacherForTheSubject;

    public Responsibility $teacherResponsibility;

    /**
     * Lifecycle method executed ones when the component is launching
     * 
     * @param Teacher $teacher
     */
    public function mount(Teacher $teacher)
    {
        $this->teacher = $teacher;

        $this->allResponsibilities = $this->getResponsibilities();
        $this->levels = $this->getLevels();
        $this->subjects = $this->getSubjects();
        $this->departments = $this->getDepartments();
        $this->levelUnits = $this->getLevelUnits();

        $this->allLevelUnitsMissingTeacherForThatSubject = collect([]);

        $this->allLevelsMissingTeacherForTheSubject = collect([]);

        $this->teacherResponsibility = Responsibility::firstOrCreate(['name' => 'Subject Teacher']);
    }

    /**
     * Executes everytime the state of the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.teacher-responsibilities', [
            'responsibilities' => $this->getTeacherResponsibilities()
        ]);
    }

    /**
     * Hook method called everytime the responsibility_id field is updated
     * 
     * @param mixed
     */
    public function updatedResponsibilityId($value)
    {
        $responsibility = $this->allResponsibilities->find($value);

        $this->fields = $responsibility->requirements ?? [];
    }

    /**
     * Hook method to select all classes
     * 
     * @param string - true|false variant
     */
    public function updatedSelectAllClasses($value)
    {
        $value = boolval($value);

        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        if($systemSettings->school_has_streams){
            if($value){
                $this->selectedClasses = array_fill_keys($this->allLevelUnitsMissingTeacherForThatSubject->pluck('id')->all(), 'true');
            }else{
                $this->selectedClasses = array_fill_keys($this->allLevelUnitsMissingTeacherForThatSubject->pluck('id')->all(), null);
            }
        }else{
            if($value){
                $this->selectedClasses = array_fill_keys($this->allLevelsMissingTeacherForTheSubject->pluck('id')->all(), 'true');
            }else{
                $this->selectedClasses = array_fill_keys($this->allLevelsMissingTeacherForTheSubject->pluck('id')->all(), null);
            }
        }
        
    }

    /**
     * Hook method called when subject teacher id is selected
     * 
     * @param string $value
     */
    public function updatedTeacherSubjectId($value)
    {

        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        if ($systemSettings->school_has_streams) {

            $leveUnitIds = DB::table('responsibility_teacher')
                ->select(['level_unit_id'])
                ->distinct('level_unit_id')
                ->where([
                    ['subject_id', $value],
                    ['responsibility_id', $this->teacherResponsibility->id]
                ])->pluck('level_unit_id')->all();
            
            $this->allLevelUnitsMissingTeacherForThatSubject = LevelUnit::whereNotIn('id', $leveUnitIds)->get();

        }else{

            $levelIds = DB::table('responsibility_teacher')
                ->select(['level_id'])
                ->distinct('level_id')
                ->where([
                    ['subject_id', $value],
                    ['responsibility_id', $this->teacherResponsibility->id]
                ])->pluck('level_id')->all();

            $this->allLevelsMissingTeacherForTheSubject = Level::whereNotIn('id', $levelIds)->get();
        }
    }

    /**
     * Get all responsibilities from the database
     * 
     * @return Collection
     */
    public function getResponsibilities()
    {
        return Responsibility::all();
    }

    /**
     * Get all levels from the database
     * 
     * @return Collection
     */
    public function getLevels()
    {
        return Level::all(['id', 'name']);
    }

    /**
     * Get all subjects from the database
     * 
     * @return Collection
     */
    public function getSubjects()
    {
        return $this->teacher->subjects;
    }

    /**
     * Get all Level Units from the database
     * 
     * @return Collection
     */
    public function getLevelUnits()
    {
        return LevelUnit::all(['id', 'alias']);
    }

    /**
     * Get all departments from he database
     * 
     * @return Collection
     */
    public function getDepartments()
    {
        return Department::all(['id', 'name']);
    }

    /**
     * Get all current teacher assigned responsibilities
     * 
     * @return Collection
     */
    public function getTeacherResponsibilities()
    {
        return $this->teacher->fresh()->responsibilities;
    }

    /**
     * Dynamic validation rules based on the responsibility selected requirements
     * 
     * @return array
     */
    public function rules()
    {
        $dynamicRules = array();

        if (in_array('level', $this->fields)) {
            $dynamicRules['level_id'] = ['bail', 'required', 'integer'];
        }

        if (in_array('department', $this->fields)) {
            $dynamicRules['department_id'] = ['bail', 'required', 'integer'];
        }

        if (in_array('class', $this->fields)) {
            $dynamicRules['level_unit_id'] = ['bail', 'required', 'integer'];
        }

        if (in_array('subject', $this->fields)) {
            $dynamicRules['subject_id'] = ['bail', 'required', 'integer'];
        }

        return array_merge($dynamicRules, [
            'responsibility_id' => ['bail', 'bail', 'required', 'integer']
        ]);
    }

    /**
     * Assign a teacher single responsibility
     */
    public function assignResponsibility()
    {
        $data = $this->validate();

        $data = array_filter($data, fn($value, $key) => !empty($value), ARRAY_FILTER_USE_BOTH);

        try {

            $this->authorize('manageTeacherResponsibilities', $this->teacher);
            
            $id = $data['responsibility_id'];

            
            $responsibility = Responsibility::findOrFail($id);
            
            $count = ResponsibilityTeacher::where($data)->count();
            
            if($count < $responsibility->how_many){

                unset($data['responsibility_id']);
                
                $this->teacher->responsibilities()->attach($id, $data);
    
                session()->flash('status', "{$this->teacher->auth->name} has been assigned a new responsibility");
    
                $this->reset(['responsibility_id', 'level_unit_id', 'level_id', 'department_id', 'subject_id']);
    
                $this->emit('hide-assign-teacher-responsibility-modal');

            }else{

                session()->flash('error', "Enough teachers already assigned the {$responsibility->name} responsibility");
    
                $this->emit('hide-assign-teacher-responsibility-modal');

            }

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to assign the responsibility";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
            $this->emit('hide-upsert-responsibility-modal');
        }
        
    }

    /**
     * Assigning bulk subject teacher responsibilities to a teacher
     */
    public function assignBulkResponsibilities()
    {
        
        $data = $this->validate([
            'teacher_subject_id' => ['bail', 'required', 'integer'],
            'selectedClasses' => ['bail', 'array', 'required', 'min:1']
        ]);

        try {

            $this->authorize('manageTeacherResponsibilities', $this->teacher);
    
            $classes = array_filter($data['selectedClasses'], fn($value, $key) => boolval($value), ARRAY_FILTER_USE_BOTH);

            /** @var SystemSettings */
            $systemSettings = app(SystemSettings::class);

            if($systemSettings->school_has_streams){

                foreach ($classes as $key => $value) {
                    
                    DB::table('responsibility_teacher')
                        ->insertOrIgnore([
                            'teacher_id' => $this->teacher->id,
                            'responsibility_id' => $this->teacherResponsibility->id,
                            'level_unit_id' => $key,
                            'subject_id' => $data['teacher_subject_id']
                        ]);
    
                }

            }else{

                foreach ($classes as $key => $value) {
                    
                    DB::table('responsibility_teacher')
                        ->insertOrIgnore([
                            'teacher_id' => $this->teacher->id,
                            'responsibility_id' => $this->teacherResponsibility->id,
                            'level_id' => $key,
                            'subject_id' => $data['teacher_subject_id']
                        ]);
                }

            }

            $this->reset(['selectAllClasses', 'teacher_subject_id', 'selectedClasses']);

            $this->allLevelUnitsMissingTeacherForThatSubject = collect([]);

            $this->allLevelsMissingTeacherForTheSubject = collect([]);

            session()->flash('status', 'Teacher successfully assigned bulk subject responsibilities');

            $this->emit('hide-assign-bulk-responsibilities-modal');
    
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to assign the responsibilities";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
            $this->emit('hide-assign-bulk-responsibilities-modal');
            
        }
        
    }

    /**
     * Remove the current teacher responsibility
     */
    public function removeResponsibility(ResponsibilityTeacher $responsibilityTeacher)
    {

        try {

            $this->authorize('manageTeacherResponsibilities', $this->teacher);
            
            if($responsibilityTeacher->delete()){

                session()->flash('status', "{$this->teacher->auth->name} responsibility has been removed");

            }
    
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = "Failed to revoke the responsibility";

            if($exception instanceof AuthorizationException) $message = $exception->getMessage();

            else $message = App::environment('local') ? $exception->getMessage() : $message;

            session()->flash('error', $message);
            
        }
    }
}
