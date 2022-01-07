<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\LevelUnit;
use Illuminate\View\Component;
use App\Settings\SystemSettings;

class LevelUnitStudentPerformance extends Component
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
        return view('components.exams.analysis.level-unit-student-performance');
    }


    /** 
     * Get the top students in the level
     * 
     * @return Collection
     * 
     */
    public function students()
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $orderByCol = ($systemSettings->school_level === 'secondary')
            ? 'mp'
            : 'mm';

        return $this->exam->students()
            ->orderByPivot($orderByCol, 'desc')
            ->where('students.level_unit_id', $this->levelUnit->id)
            ->limit(5)
            ->get();
    }

    /**
     * Count the total number of table columns available
     * 
     * @return int
     */
    public function colsCount() : int
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        if ($systemSettings->school_has_streams) {
            if ($systemSettings->school_level === 'secondary') {
                return 8;
            }else{
                return 7;
            }
        }else{
            if ($systemSettings->school_level === 'secondary') {
                return 7;
            }else{
                return 6;
            }
        }
        
    }    
}
