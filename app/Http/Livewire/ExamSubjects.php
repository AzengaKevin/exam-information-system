<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class ExamSubjects extends Component
{
    /** @var Exam */
    public $exam;

    /**
     * Lifecycle method called once when the component is mounting
     * 
     * @param Exam $exam
     */
    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * Get all enrolled subjects for the current exam
     * 
     * @return Collection
     */
    public function getEnrolledSubjects()
    {
        return $this->exam->subjects;
    }

    /**
     * Lifecyce method that renders and re-renders the component when the state of the
     * exam-subjects component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.exam-subjects', [
            'subjects' => $this->getEnrolledSubjects()
        ]);
    }
}
