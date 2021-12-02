<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;

class ExamSubjects extends Component
{
    /** @var Exam */
    public $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function getEnrolledSubjects()
    {
        return $this->exam->subjects;
    }

    public function render()
    {
        return view('livewire.exam-subjects', [
            'subjects' => $this->getEnrolledSubjects()
        ]);
    }
}
