<?php

namespace App\Http\Livewire;

use App\Models\Department;
use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Responsibility;
use App\Models\ResponsibilityTeacher;
use App\Models\Teacher;
use Illuminate\Support\Collection;
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

    public function mount(Teacher $teacher)
    {
        $this->teacher = $teacher;

        $this->allResponsibilities = $this->getResponsibilities();
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
