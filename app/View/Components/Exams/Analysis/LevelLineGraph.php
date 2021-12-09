<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Level;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class LevelLineGraph extends Component
{
    public Exam $exam;
    public Level $level;

    public array $levelUnitsPointsData;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Exam $exam, Level $level)
    {
        $this->exam = $exam;
        $this->level = $level;

        $this->levelUnitsPointsData = $this->levelUnitsData();
    }

    public function levelUnitsData()
    {
        $levelUnits = $this->level->levelUnits()->with('stream')->get();

        $pointsData = DB::table('level_units')
            ->select(['level_units.id', 'exam_level_unit.points'])
            ->leftJoin('exam_level_unit', 'level_units.id', '=', 'exam_level_unit.level_unit_id')
            ->where('level_units.level_id', $this->level->id)
            ->where('exam_level_unit.exam_id', $this->exam->id)
            ->get(['id', 'points'])
            ->pluck('points', 'id')
            ->toArray();
        
        $data = array();

        foreach ($levelUnits as $levelUnit) {
            $data[$levelUnit->stream->slug] = $pointsData[$levelUnit->id] ?? 0;
        }
        
        return $data;
    }

    /**
     * Get the count of students that did the exam in the current level
     */
    public function studentsCount()
    {
        $tblName = Str::slug($this->exam->shortname);

        return DB::table($tblName)->where('level_id', $this->level->id)->count();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.analysis.level-line-graph');
    }
}
