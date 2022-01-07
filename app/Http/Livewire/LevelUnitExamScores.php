<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Grading;
use Livewire\Component;
use App\Models\LevelUnit;
use App\Settings\SystemSettings;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LevelUnitExamScores extends Component
{
    /** @var Exam */
    public Exam $exam;

    public LevelUnit $levelUnit;

    public $admno;

    public $col;

    public function mount(Exam $exam, LevelUnit $levelUnit)
    {
        $this->exam = $exam;
        $this->levelUnit = $levelUnit;
    }

    public function render()
    {
        return view('livewire.level-unit-exam-scores', [
            'data' => $this->getResults(),
            'cols' => $this->getColumns(),
            'subjectCols' => $this->getSubjectColumns(),
            'rankCols' => $this->getRankColumns()
        ]);
    }

    /**
     * Get columns that can be used for ranking students
     */
    public function getRankColumns() : array
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);
        
        $columns = array('tm' => 'Total Score');

        if($systemSettings->school_level === 'secondary') $columns['tp'] = 'Aggregate Points';
        
        return $columns;
    }


    public function getResults()
    {
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $columns = $this->exam->subjects->pluck("shortname")->toArray();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        return Schema::hasTable($tblName)
            ? DB::table($tblName)
                ->select(array_merge(["admno"], $columns, $aggregateCols))
                ->addSelect("students.name", "level_units.alias")
                ->join("students", "{$tblName}.admno", '=', 'students.adm_no')
                ->join("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id')
                ->where("{$tblName}.level_unit_id", $this->levelUnit->id)
                ->orderBy("sp")
                ->paginate(24)
            : collect([]);
    }

    /**
     * Retrieve all columns for the exam
     * 
     * @return array
     */
    public function getSubjectColumns(): array
    {
       return $this->exam->subjects->pluck("shortname")->toArray();
    }

    /**
     * Appropriate aggregate columns based system settings
     * @return array
     */
    public function getAggregateColumns(): array
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
     * Get a combination of all columns
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
        $studentLevelCols = array("name", "alias");

        return array_merge($studentLevelCols, $columns, $aggregateCols);;
    }

    /**
     * Generate aggregates for each and every record in the current exam scores table
     */
    public function generateBulkAggregates()
    {
        try {
            
            $cols = $this->getSubjectColumns();

            $tblName = Str::slug($this->exam->shortname);

            /** @var Collection */
            $data = DB::table($tblName)
                ->where("level_unit_id", $this->levelUnit->id)
                ->select(array_merge(["admno"], $cols))->get();

            $data->each(function($stuData) use($tblName, $cols){
                $totalScore = 0;
                $totalPoints = 0;
                $populatedCols = 0;

                foreach ($cols as $col) {

                    if(!is_null($stuData->$col)){
                        $populatedCols++;

                        $subData = json_decode($stuData->$col);

                        $totalScore += $subData->score ?? 0;
                        $totalPoints += $subData->points ?? 0;
                    }
                }

                $avgPoints = round($totalPoints / $populatedCols);
                $avgScore = round($totalScore / $populatedCols);

                $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

                $avgGrade = $pgm[$avgPoints];

                DB::table($tblName)
                ->updateOrInsert([
                    "admno" => $stuData->admno
                ], [
                    "mm" => $avgScore,
                    "mg" => $avgGrade,
                    'mp' => $avgPoints,
                    'tp' => $totalPoints,
                    'tm' => $totalScore
                ]);
            });

            session()->flash('status', 'Aggregates for the whole class successfully generated');

            $this->emit('hide-generate-scores-aggregates-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred');

            $this->emit('hide-generate-scores-aggregates-modal');
            
        }
        
    }

    /**
     * Triggers Javascript to show modal for generating aggregates
     * 
     * @param string $admno
     */
    public function showGenerateAggregatesModal(string $admno)
    {
        $this->admno = $admno;

        $this->emit('show-generate-scores-aggregates-modal');
        
    }

    /**
     * Generate aggregates for a single scores table record i.e student
     */
    public function generateAggregates()
    {

        try {
            
            $cols = $this->getSubjectColumns();

            $tblName = Str::slug($this->exam->shortname);

            $stuData = DB::table($tblName)
                ->where("level_unit_id", $this->levelUnit->id)
                ->where('admno', $this->admno)
                ->select(array_merge(["admno"], $cols))->first();

            $totalScore = 0;
            $totalPoints = 0;
            $populatedCols = 0;

            foreach ($cols as $col) {

                if(!is_null($stuData->$col)){
                    $populatedCols++;

                    $subData = json_decode($stuData->$col);

                    $totalScore += $subData->score ?? 0;
                    $totalPoints += $subData->points ?? 0;
                }
            }

            $avgPoints = intval($totalPoints / $populatedCols);
            $avgScore = intval($totalScore / $populatedCols);

            $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

            $avgGrade = $pgm[$avgPoints];

            DB::table($tblName)
            ->updateOrInsert([
                "admno" => $stuData->admno
            ], [
                "mm" => $avgScore,
                "mg" => $avgGrade,
                'mp' => $avgPoints,
                'tp' => $totalPoints,
                'tm' => $totalScore
            ]);

            $this->reset('admno');

            session()->flash('status', "Aggregates for {$this->admno} class successfully generated");

            $this->emit('hide-generate-scores-aggregates-modal');   

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            $this->reset('admno');

            session()->flash('error', 'A fatal error occurred');

            $this->emit('hide-generate-scores-aggregates-modal');
            
        }
        
    }

    /**
     * Publishes scores for the current level unit | class
     */
    public function publishClassScores()
    {
        try {

            $tblName = Str::slug($this->exam->shortname);

            $data = DB::table($tblName)
                ->where("level_unit_id", $this->levelUnit->id)
                ->selectRaw("AVG(tm) AS avg_total, AVG(mp) avg_points")
                ->first();
            
            $avgTotal = number_format($data->avg_total, 2);
            $avgPoints = number_format($data->avg_points, 4);

            $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

            $avgGrade = $pgm[intval(round($avgPoints))];

            $this->exam->levelUnits()->syncWithoutDetaching([
                $this->levelUnit->id => [
                    "points" => $avgPoints,
                    "grade" => $avgGrade,
                    "average" => $avgTotal
                ]
            ]);

            session()->flash('status', 'Your class scores have been successfully published, you can republish the scores incase of any changes');

            $this->emit('hide-publish-class-scores-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred');

            $this->emit('hide-publish-class-scores-modal');

        }
    }

    /**
     * Generate stream ranks based on the selected aggregate columns
     */
    public function generateRanks()
    {
        $data = $this->validate(['col' => ['nullable', 'string', Rule::in(array_keys($this->getRankColumns()))]]);

        $col = $data['col'] ?? 'tm';

        try {

            $tblName = Str::slug($this->exam->shortname);

            // Get order records from the databas with the admno number as the primary key

            /** @var Collection */
            $data = DB::table($tblName)
                ->select(['admno', $col])
                ->where('level_unit_id', $this->levelUnit->id)
                ->orderBy($col, 'desc')
                ->get();

            $data->each(function($item, $key) use($tblName){

                $rank = $key + 1;

                DB::table($tblName)->updateOrInsert(['admno' => $item->admno],['sp' => $rank]);
                
            });

            session()->flash('status', 'Student ranking operation completed successfully');

            $this->emit('hide-rank-class-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred while trying to rank students');

            $this->emit('hide-rank-class-modal');
        }   
        
    }

    /**
     * Publish current exam level unit grade distribution
     */
    public function publishLevelUnitGradeDistribution()
    {
        
        try {
            
            $tblName = Str::slug($this->exam->shortname);

            /** @var Collection */
            $data = DB::table($tblName)
                ->where('level_unit_id', $this->levelUnit->id)
                ->selectRaw("mg, COUNT(mg) AS grade_count")
                ->distinct("mg")
                ->groupBy('mg')
                ->get()
                ->pluck('grade_count', 'mg');

            if ($data->count()) {

                DB::beginTransaction();
    
                foreach (Grading::gradeOptions() as $grade) {

                    DB::table('exam_level_unit_grade_distribution')
                        ->updateOrInsert([
                            'exam_id' => $this->exam->id,
                            'level_unit_id' => $this->levelUnit->id,
                            'grade' => $grade,
                        ],['grade_count' => $data[$grade] ?? 0]);
                }
                DB::commit();
    
                session()->flash('status', 'Level Unit grade distribution has been successfully published');
            }

            $this->emit('hide-publish-level-unit-grade-dist-modal');
            
        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred, consult admin');

            $this->emit('hide-publish-level-unit-grade-dist-modal');
            
        }
        
    }
}
