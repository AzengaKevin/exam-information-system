<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Level;
use App\Models\LevelUnit;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExamResults extends Component
{

    /** @var Exam */
    public $exam;

    public $level_unit_id;
    public $level_id;
    public $name;
    public $admno;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
        
    }

    public function render()
    {
        $this->getResults();
        return view('livewire.exam-results', [
            'data' => $this->getResults(),
            'cols' => $this->getColumns(),
            'subjectCols' => $this->getSubjectColumns(),
            'levels' => $this->getLevels(),
            'levelUnits' => $this->getLevelUnits()
        ]);
    }

    public function getResults()
    {
        $tblName = Str::slug($this->exam->shortname);
        /** @var array */
        $columns = $this->exam->subjects->pluck("shortname")->toArray();

        /** @var array */
        $aggregateCols = array("average", "total");
        
        if(!Schema::hasTable($tblName)) return new Paginator([], 16);
        
        $query = DB::table($tblName)
            ->select(array_merge(["admno"], $columns, $aggregateCols))
            ->addSelect("students.name", "level_units.alias")
            ->join("students", "{$tblName}.admno", '=', 'students.adm_no')
            ->join("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id');

        if($this->level_id) $query->where("{$tblName}.level_id", $this->level_id);

        if($this->level_unit_id) $query->where("{$tblName}.level_unit_id", $this->level_unit_id);

        if($this->admno) $query->where("{$tblName}.admno", $this->admno);

        return $query->paginate(16);
    }

    public function getSubjectColumns()
    {
       return $this->exam->subjects->pluck("shortname")->toArray(); 
    }

    public function getColumns()
    {
        /** @var array */
        $columns = $this->exam->subjects->pluck("shortname")->toArray();

        /** @var array */
        $aggregateCols = array("average", "total");

        /** @var array */
        $studentLevelCols = array("name", "admno", "alias");

        return array_merge($studentLevelCols, $columns, $aggregateCols);;
    }

    public function getLevels()
    {
        return Level::all(['id', 'name']);
    }

    public function getLevelUnits()
    {
        return LevelUnit::all(['id', 'alias']);
    }

}
