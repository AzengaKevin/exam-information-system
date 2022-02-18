<?php

namespace App\Http\Livewire;

use App\Models\Student;
use App\Services\SubjectService;
use Livewire\Component;

class StudentSubjects extends Component
{

    public Student $student;

    public $compulsorySubjects;

    /**
     * Lifecycle method that executes only once when the component is launching
     * 
     * @param Student $student
     */
    public function mount(SubjectService $subjectService, Student $student)
    {
        $this->student = $student;
        $this->compulsorySubjects = $subjectService->getCompulsorySubjects();
    }

    /**
     * Lifecycle method that renders the component everytime the state of the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.student-subjects', [
            'optionalSubjects' => $this->getStudentOptionalSubjects()
        ]);
    }

    /**
     * Get current student optional subjects
     * 
     * @return Collection
     */
    public function getStudentOptionalSubjects()
    {
        /** @var SubjectService */
        $studentService = app(SubjectService::class);

        return $this->student->optionalSubjects()->count()
            ? $this->student->optionalSubjects
            : $studentService->getFilteredOptionalSubjects([
                'level_id' => $this->student->level_id,
                'stream_id' => $this->student->stream_id
            ]);
    }
}
