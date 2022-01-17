<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Level;
use Illuminate\View\Component;
use App\Settings\SystemSettings;

class LevelMostDroppedStudents extends Component
{
    public Exam $exam;
    public Level $level;
    
    /**
     * Create a new component instance.
     * 
     * @param Exam $exam
     * @param Level $exam
     *
     * @return void
     */
    public function __construct(Exam $exam, Level $level)
    {
        $this->exam = $exam;
        $this->level = $level;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.analysis.level-most-dropped-students');
    }

    /** 
     * Get the most improved students
     * 
     * @return Collection
     * 
     */
    public function students()
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $orderByCol = ($systemSettings->school_level === 'secondary')
            ? 'mpd'
            : 'mmd';

        return $this->exam->students()
            ->orderByPivot($orderByCol, 'asc')
            ->wherePivot($orderByCol, '<', 0)
            ->where('students.level_id', $this->level->id)
            ->limit(5)
            ->get();
    }

}
