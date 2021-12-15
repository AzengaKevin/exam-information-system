<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Level;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Illuminate\Support\Str;

class LevelExamResults extends Component
{
    public Exam $exam;
    public Level $level;
    
    public $level_unit_id;
    public $name;
    public $admno;

    public $orderBy = 'op';

    public function mount(Exam $exam, Level $level)
    {
        $this->exam = $exam;
        $this->level = $level;
    }

    public function render()
    {
        return view('livewire.level-exam-results', [
            'data' => $this->getResults(),
            'cols' => $this->getColumns(),
            'subjectCols' => $this->getSubjectColumns(),
        ]);
    }

    public function getResults()
    {
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $columns = $this->getSubjectColumns();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        if(Schema::hasTable($tblName)){

            $query = DB::table($tblName)
                ->select(array_merge(["admno"], $columns, $aggregateCols))
                ->addSelect("students.name", "level_units.alias")
                ->join("students", "{$tblName}.admno", '=', 'students.adm_no')
                ->join("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id')
                ->where("{$tblName}.level_id", $this->level->id)
                ->orderBy($this->orderBy ?? 'op');
            
            if (!empty($this->level_unit_id)) {
                $query->where("{$tblName}.level_unit_id", $this->level_unit_id);
            }

            if (!empty($this->admno)) {
                $query->where("{$tblName}.admno", $this->admno);
            }

            if(!empty($this->name)){
                $query->where('students.name', 'LIKE', "%{$this->name}%");
            }

            return $query->paginate(24, ['*'], Str::slug($this->level->name));

        }else{
            return new Paginator([], 24);
        }
    }

    public function getSubjectColumns() : array
    {
       return $this->exam->subjects->pluck("shortname")->toArray();
    }

    public function getAggregateColumns() : array
    {
        return array("mm", "tm", "mg", "mp",  "tp", "sp", "op");
    }

    public function getColumns()
    {
        /** @var array */
        $columns = $this->exam->subjects->pluck("shortname")->toArray();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        /** @var array */
        $studentLevelCols = array("admno", "name", "alias");

        return array_merge($studentLevelCols, $columns, $aggregateCols);;
    }
    
}
