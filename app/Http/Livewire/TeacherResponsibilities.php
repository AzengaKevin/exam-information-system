<?php

namespace App\Http\Livewire;

use App\Models\Department;
use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Responsibility;
use App\Models\ResponsibilityTeacher;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class TeacherResponsibilities extends Component
{
    public Teacher $teacher;

    public $responsibility_id;
    public $level_unit_id;
    public $level_id;
    public $department_id;
    public $subject_id;

    public function mount(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function render()
    {
        return view('livewire.teacher-responsibilities', [
            'responsibilities' => $this->getTeacherResponsibilities(),
            'responsibilityOptions' => $this->getResponsibilities(),
            'levels' => $this->getLevels(),
            'subjects' => $this->getSubjects(),
            'departments' => $this->getDepartments(),
            'levelUnits' => $this->getLevelUnits()
        ]);
    }

    public function getResponsibilities()
    {
        return Responsibility::all(['id', 'name']);
    }

    public function getLevels()
    {
        return Level::all(['id', 'name']);
    }

    public function getSubjects()
    {
        return Subject::all(['id', 'name']);
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
        return $this->teacher->responsibilities;
    }

    public function rules()
    {
        return [
            'responsibility_id' => ['bail', 'required', 'integer'],
            'department_id' => ['nullable', 'integer'],
            'level_id' => ['nullable', 'integer'],
            'level_unit_id' => ['nullable', 'integer'],
            'subject_id' => ['nullable', 'integer'],
        ];
    }

    public function assignResponsibility()
    {
        $data = $this->validate();

        try {

            $id = $data['responsibility_id'];

            unset($data['responsibility_id']);
            
            $this->teacher->responsibilities()->attach($id, $data);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'teacher' => $this->teacher->id,
            ]);
        }
        
    }
}
