<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\LevelUnit;
use Illuminate\View\Component;

class LevelUnitTopStudentsInSubject extends Component
{
    public Exam $exam;
    public LevelUnit $levelUnit;
    public Subject $subject;

    /**
     * Create a new component instance.
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     * @param Subject $subject
     *
     * @return void
     */
    public function __construct(Exam $exam, LevelUnit $levelUnit, Subject $subject)
    {
        $this->exam = $exam;
        $this->levelUnit = $levelUnit;
        $this->subject = $subject;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.analysis.level-unit-top-students-in-subject');
    }

    /**
     * Get all exam level-unit subject top student ordered by the score
     * 
     * @return Collection
     */
    public function students()
    {
        return $this->exam->levelUnitTopSubjectStudents()
            ->wherePivot('level_unit_id', $this->levelUnit->id)
            ->wherePivot('subject_id', $this->subject->id)
            ->orderByPivot('score', 'desc')
            ->get();
    }
}
