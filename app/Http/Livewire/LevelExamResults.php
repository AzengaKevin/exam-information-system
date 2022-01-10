<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Level;
use App\Settings\SystemSettings;
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

    /**
     * Component lifecycle method called once when the component Launches
     * 
     * @param Exam $exam
     * @param Level $level
     */
    public function mount(Exam $exam, Level $level)
    {
        $this->exam = $exam;
        $this->level = $level;
    }

    /**
     * Called everytime the component renders|re-renders
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.level-exam-results', [
            'data' => $this->getResults(),
            'cols' => $this->getColumns(),
            'subjectCols' => $this->getSubjectColumns(),
        ]);
    }

    /**
     * Get all level results from the database
     * 
     * @return Paginator
     * 
     */
    public function getResults()
    {
        // Get the table name
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $columns = $this->getSubjectColumns();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        if(Schema::hasTable($tblName)){

            $query = DB::table($tblName)
                ->select(array_merge(["students.adm_no AS admno"], $columns, $aggregateCols))
                ->addSelect("students.name", "level_units.alias")
                ->join("students", "{$tblName}.student_id", '=', 'students.id')
                ->leftJoin("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id')
                ->where("{$tblName}.level_id", $this->level->id)
                ->orderBy($this->orderBy ?? 'op');
            
            if (!empty($this->level_unit_id)) {
                $query->where("{$tblName}.level_unit_id", $this->level_unit_id);
            }

            if (!empty($this->admno)) {
                $query->where("admno", $this->admno);
            }

            if(!empty($this->name)){
                $query->where('students.name', 'LIKE', "%{$this->name}%");
            }

            return $query->paginate(24, ['*'], Str::slug($this->level->name));

        }else{
            return new Paginator([], 24);
        }
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

        $cols = array("mm", "tm", "op");

        if($systemSettings->school_level == 'secondary'){
            array_push($cols, "mg", "mp", "tp");
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
        $columns = $this->exam->subjects->pluck("shortname")->toArray();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        /** @var array */
        $studentLevelCols = array("admno", "name", "alias");

        return array_merge($studentLevelCols, $columns, $aggregateCols);
    }
    
}
