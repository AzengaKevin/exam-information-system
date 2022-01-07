<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Level;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class LevelGradeDistribution extends Component
{
    public Exam $exam;
    public Level $level;

    /**
     * Create a new component instance.
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
        return view('components.exams.analysis.level-grade-distribution', [
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
        return DB::table('exam_level_grade_distribution')
            ->where([['level_id', $this->level->id],['exam_id', $this->exam->id]])
            ->select(['grade', 'grade_count'])
            ->get(['grade', 'grade_count'])
            ->pluck('grade_count', 'grade')
            ->toArray();
    }
}
