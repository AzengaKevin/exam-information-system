<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use App\Settings\SystemSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class LevelUnitExamResults extends Component
{
    public Exam $exam;
    public LevelUnit $levelUnit;

    /**
     * Lifecycle method that execute once when the component is mounting
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     */
    public function mount(Exam $exam, LevelUnit $levelUnit)
    {
        $this->exam = $exam;
        $this->levelUnit = $levelUnit;
    }

    /**
     * Lifecycle method executes everytime the component state changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.level-unit-exam-results', [
            'data' => $this->getResults(),
            'cols' => $this->getColumns(),
            'subjectCols' => $this->getSubjectColumns(),
        ]);
    }

    /**
     * Get all level-unit results from the database
     * 
     * @return Paginator
     * 
     */
    public function getResults()
    {
        // Get the table name
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $subjectColumns = $this->getSubjectColumns();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        $query = DB::table($tblName)
            ->select(array_merge(["students.adm_no AS admno"], $subjectColumns, $aggregateCols))
            ->addSelect("students.name", "level_units.alias")
            ->join("students", "{$tblName}.student_id", '=', 'students.id')
            ->leftJoin("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id')
            ->where("{$tblName}.level_unit_id", $this->levelUnit->id)
            ->orderBy('sp');

        return $query->paginate(24, ['*'], Str::slug($this->levelUnit->alias))->withQueryString();

    }

    /**
     * Get exam subject columns
     * 
     * @return array
     */
    public function getSubjectColumns() : array
    {
       return $this->exam->subjects->pluck("shortname")->toArray();
    }

    /**
     * Get appropriate level columns
     * 
     * @return array
     */
    public function getAggregateColumns() : array
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $cols = array("mm", "tm", "tmd", "op");

        if($systemSettings->school_level == 'secondary'){
            array_push($cols, "mg", "mp", "tp", "tpd");
        }

        if ($systemSettings->school_has_streams) {
            array_push($cols, "sp");
        }

        return $cols;
    }

    /**
     * Gets a combination of all cols to show on the merit list
     * 
     * @return array
     */
    public function getColumns() : array
    {
        /** @var array */
        $columns = $this->exam->subjects->pluck("shortname")->all();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        /** @var array */
        $studentLevelCols = array("admno", "name", "alias");

        return array_merge($studentLevelCols, $columns, $aggregateCols);
    }

}
