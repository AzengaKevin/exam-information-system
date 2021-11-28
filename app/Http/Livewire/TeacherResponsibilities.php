<?php

namespace App\Http\Livewire;

use App\Models\ResponsibilityTeacher;
use App\Models\Teacher;
use Livewire\Component;

class TeacherResponsibilities extends Component
{
    public Teacher $teacher;

    public function mount(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function render()
    {
        return view('livewire.teacher-responsibilities', [
            'responsibilities' => $this->getAllResponsibilities()
        ]);
    }

    public function getAllResponsibilities()
    {
        // return ResponsibilityTeacher::with(['level', 'subject', 'department', 'teacher.auth', 'responsibility', 'levelUnit'])
        //     ->for($this->teacher)
        //     ->get();

        return $this->teacher->responsibilities;
    }
}
