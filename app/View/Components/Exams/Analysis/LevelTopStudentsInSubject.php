<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\View\Component;

class LevelTopStudentsInSubject extends Component
{
    public Exam $exam;
    public Level $level;
    public Subject $subject;

    /**
     * Create a new component instance.
     * 
     * @param Exam $exam
     * @param Level $level
     * @param Subject $subject
     *
     * @return void
     */
    public function __construct(Exam $exam, Level $level, Subject $subject)
    {
        $this->exam = $exam;
        $this->level = $level;
        $this->subject = $subject;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.analysis.level-top-students-in-subject');
    }

    /**
     * Get all exam level subject top student ordered by the score
     * 
     * @return Collection
     */
    public function students()
    {
        return $this->exam->levelTopSubjectStudents()
            ->wherePivot('level_id', $this->level->id)
            ->wherePivot('subject_id', $this->subject->id)
            ->orderByPivot('score', 'desc')
            ->get();
    }
}
