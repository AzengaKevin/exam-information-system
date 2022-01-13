<?php

namespace App\Http\Livewire;

use App\Models\Department;
use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Responsibility;
use App\Models\ResponsibilityTeacher;
use App\Models\Teacher;
use App\Settings\SystemSettings;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TeacherResponsibilities extends Component
{
    public Teacher $teacher;

    public Collection $allResponsibilities;

    public $responsibility_id;
    public $level_unit_id;
    public $level_id;
    public $department_id;
    public $subject_id;

    public $fields = [];

    public $type = null;

    public $selectAllClasses;
    public $teacher_subject_id;

    public $selectedClasses = [];

    public Collection $allLevelUnitsMissingTeacherForThatSubject;

    public Responsibility $teacherResponsibility;

    public function mount(Teacher $teacher)
    {
        $this->teacher = $teacher;

        $this->allResponsibilities = $this->getResponsibilities();

        $this->allLevelUnitsMissingTeacherForThatSubject = collect([]);

        $this->teacherResponsibility = Responsibility::firstOrCreate(['name' => 'Subject Teacher']);
    }

    public function render()
    {
        return view('livewire.teacher-responsibilities', [
            'responsibilities' => $this->getTeacherResponsibilities(),
            'responsibilityOptions' => $this->allResponsibilities,
            'levels' => $this->getLevels(),
            'subjects' => $this->getSubjects(),
            'departments' => $this->getDepartments(),
            'levelUnits' => $this->getLevelUnits()
        ]);
    }

    public function updatedResponsibilityId($value)
    {
        $responsibility = $this->allResponsibilities->find($value);

        $this->fields = $responsibility->requirements;
    }

    public function updatedSelectAllClasses($value)
    {
        $value = boolval($value);
        
        if($value){
            
            foreach ($this->allLevelUnitsMissingTeacherForThatSubject as $leveUnit) {

                $this->selectedClasses[$leveUnit->id] = 'true';
                
            }

        }else{
            
            foreach ($this->allLevelUnitsMissingTeacherForThatSubject as $leveUnit) {

                $this->selectedClasses[$leveUnit->id] = null;
                
            }

        }
    }

    public function updatedTeacherSubjectId($value)
    {

        $leveUnitIds = DB::table('responsibility_teacher')->select(['level_unit_id'])
            ->where([
                ['subject_id', $value],
                ['responsibility_id', $this->teacherResponsibility->id]
            ])->pluck('level_unit_id')->toArray();
        
        $this->allLevelUnitsMissingTeacherForThatSubject = LevelUnit::whereNotIn('id', $leveUnitIds)->get();
        
    }

    public function getResponsibilities()
    {
        return Responsibility::all();
    }

    public function getLevels()
    {
        return Level::all(['id', 'name']);
    }

    public function getSubjects()
    {
        return $this->teacher->subjects;
    }

    public function getLevelUnits()
    {
        return LevelUnit::all(['id', 'alias']);
    }

    public function getDepartments()
    {
        return Department::all(['id', 'name']);
    }

    public function getTeacherResponsibilities()
    {
        return $this->teacher->fresh()->responsibilities;
    }

    public function rules()
    {
        $dynamicRules = array();

        if (in_array('level', $this->fields)) {
            $dynamicRules['level_id'] = ['required', 'integer'];
        }

        if (in_array('department', $this->fields)) {
            $dynamicRules['department_id'] = ['required', 'integer'];
        }

        if (in_array('class', $this->fields)) {
            $dynamicRules['level_unit_id'] = ['required', 'integer'];
        }

        if (in_array('subject', $this->fields)) {
            $dynamicRules['subject_id'] = ['required', 'integer'];
        }

        return array_merge($dynamicRules, [
            'responsibility_id' => ['bail', 'required', 'integer']
        ]);
    }

    public function assignResponsibility()
    {
        $data = $this->validate();

        $data = array_filter($data, fn($value, $key) => !empty($value), ARRAY_FILTER_USE_BOTH);

        try {
            
            if(ResponsibilityTeacher::where($data)->doesntExist()){

                $id = $data['responsibility_id'];
    
                unset($data['responsibility_id']);
                
                $this->teacher->responsibilities()->attach($id, $data);
    
                session()->flash('status', "{$this->teacher->auth->name} has been assigned a new responsibility");
    
                $this->reset(['responsibility_id', 'level_unit_id', 'level_id', 'department_id', 'subject_id']);
    
                $this->emit('hide-assign-teacher-responsibility-modal');

            }else{

                session()->flash('error', 'The responsibility is already assigned to another teacher');
    
                $this->emit('hide-assign-teacher-responsibility-modal');

            }


        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'teacher' => $this->teacher->id,
            ]);
        }
        
    }

    /**
     * Assigning bulk subject teacher responsibilities to a teacher
     */
    public function assignBulkResponsibilities()
    {

        try {
            
            $data = $this->validate([
                'teacher_subject_id' => ['bail', 'required', 'integer'],
                'selectedClasses' => ['bail', 'array', 'required', 'min:1']
            ]);
    
            $classes = array_filter($data['selectedClasses'], fn($value, $key) => boolval($value), ARRAY_FILTER_USE_BOTH);

            foreach ($classes as $key => $value) {
                
                DB::table('responsibility_teacher')
                    ->insertOrIgnore([
                        'teacher_id' => $this->teacher->id,
                        'responsibility_id' => $this->teacherResponsibility->id,
                        'level_unit_id' => $key,
                        'subject_id' => $data['teacher_subject_id']
                    ]);

            }

            $this->reset(['selectAllClasses', 'teacher_subject_id', 'selectedClasses']);

            $this->allLevelUnitsMissingTeacherForThatSubject = collect([]);

            session()->flash('status', 'Teacher successfully assigned bulk subject responsibilities');

            $this->emit('hide-assign-bulk-responsibilities-modal');
    
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'teacher' => $this->teacher->id,
            ]);
    
            session()->flash('error', "A fatal DB error occurred");

            $this->emit('hide-assign-bulk-responsibilities-modal');
            
        }
        
    }

    public function removeResponsibility(ResponsibilityTeacher $responsibilityTeacher)
    {

        try {
            
            if($responsibilityTeacher->delete()){

                session()->flash('status', "{$this->teacher->auth->name} responsibility has been removed");

            }
    
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'teacher' => $this->teacher->id,
            ]);
    
            session()->flash('error', "No such responsibility for {$this->teacher->auth->name}");
            
        }
    }
}
