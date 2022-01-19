<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\LevelUnit;
use Illuminate\View\Component;
use App\Settings\SystemSettings;

class LevelUnitMostImprovedStudents extends Component
{
    public Exam $exam;
    public LevelUnit $levelUnit;

    /**
     * Create a new component instance.
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     *
     * @return void
     */
    public function __construct(Exam $exam, LevelUnit $levelUnit)
    {
        $this->exam = $exam;
        $this->levelUnit = $levelUnit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.analysis.level-unit-most-improved-students');
    }

    /** 
     * Get the most improved students in the current level unit
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
            ->orderByPivot($orderByCol, 'desc')
            ->wherePivot($orderByCol, '>', 0)
            ->where('students.level_unit_id', $this->levelUnit->id)
            ->limit(5)
            ->get();
    }    
}
