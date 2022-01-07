<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Level;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use App\Settings\SystemSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class LevelLineGraph extends Component
{
    public Exam $exam;
    public Level $level;

    public array $levelUnitsPointsData;

    public $levelWithData;

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

        $this->levelWithData = $this->exam->levels()
            ->where('exam_level.level_id', $this->level->id)
            ->first();
    }

    public function levelUnitsData()
    {
        $levelUnits = $this->level->levelUnits()->with('stream')->get();

        /** @var Collection */
        $allData = DB::table('level_units')
            ->select(['level_units.id', 'exam_level_unit.points', 'exam_level_unit.average'])
            ->leftJoin('exam_level_unit', 'level_units.id', '=', 'exam_level_unit.level_unit_id')
            ->where('level_units.level_id', $this->level->id)
            ->where('exam_level_unit.exam_id', $this->exam->id)
            ->get(['id', 'points', 'average']);

        $info = array();
        
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        if ($systemSettings->school_level === 'secondary') {
            $info = $allData->pluck('points', 'id')->toArray();
        }else{
            $info = $allData->pluck('average', 'id')->toArray();
        }
        
        $data = array();

        foreach ($levelUnits as $levelUnit) {
            $data[$levelUnit->stream->slug] = $info[$levelUnit->id] ?? 0;
        }
        
        return $data;
    }

    /**
     * Get the count of students that did the exam in the current level
     * 
     * @return int
     */
    public function studentsCount() : int
    {
        return $this->exam->students()
            ->where('students.level_id', $this->level->id)
            ->count();
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
