<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\LevelUnit;
use App\Settings\SystemSettings;
use Illuminate\View\Component;

class LevelUnitSubjectPerformance extends Component
{
    public LevelUnit $levelUnit;
    public Exam $exam;
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
        return view('components.exams.analysis.level-unit-subject-performance');
    }

    /**
     * Get level unit subjects with performamnce
     * 
     * @return Collection
     */
    public function subjects()
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $orderByCol = ($systemSettings->school_level === 'secondary')
            ? 'points'
            : 'average';

        return $this->exam->levelUnitSubjectPerformance()
            ->wherePivot('level_unit_id', $this->levelUnit->id)
            ->orderByPivot($orderByCol, 'desc')
            ->get();
    }
}
