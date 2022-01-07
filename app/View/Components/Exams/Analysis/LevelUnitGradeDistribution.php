<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\LevelUnit;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class LevelUnitGradeDistribution extends Component
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
        return view('components.exams.analysis.level-unit-grade-distribution', [
            'gradeDist' => $this->gradeDistribution()
        ]);
    }
    
    /**
     * Get the grade distribution of the currect exam in the current level
     * 
     * @return array
     */
    public function gradeDistribution()
    {
        
        return DB::table('exam_level_unit_grade_distribution')
            ->where([['level_unit_id', $this->levelUnit->id],['exam_id', $this->exam->id]])
            ->select(['grade', 'grade_count'])
            ->get(['grade', 'grade_count'])
            ->pluck('grade_count', 'grade')
            ->toArray();
    }
}
