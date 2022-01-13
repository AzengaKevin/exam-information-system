<?php

namespace App\View\Components\Exams\Scores;

use App\Models\Exam;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class LevelUnitScores extends Component
{
    public Exam $exam;

    /**
     * Create a new component instance.
     * 
     * @param Exam $exam
     *
     * @return void
     */
    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.scores.level-unit-scores');
    }

    /**
     * Retrieve exam level units with scores
     * 
     * @return Collection
     */
    public function groupedLevelUnitWithScores()
    {
        return DB::table('level_units')
            ->leftJoin("exam_level_unit", function($join){
                $join->on("level_units.id", "=", "exam_level_unit.level_unit_id")
                    ->where("exam_id", $this->exam->id);
            })
            ->leftJoin("levels", "level_units.level_id", "levels.id")
            ->select(["average", "alias", "name", "level_units.id"])
            ->whereIn('level_units.level_id', $this->exam->levels->pluck('id')->all())
            ->get()
            ->groupBy("name");
    }
}
