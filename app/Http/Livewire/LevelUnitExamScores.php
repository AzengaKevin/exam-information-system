<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Grading;
use Livewire\Component;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

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

    public function getRankColumns() : array
    {
        return [
            'points' => 'Aggregate Points',
            'total' => 'Total Score'
        ];
    }


    public function getResults()
    {
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $columns = $this->exam->subjects->pluck("shortname")->toArray();

        /** @var array */
        $aggregateCols = array("average", "total", "grade", "points", "level_unit_position", "level_position");

        return Schema::hasTable($tblName)
            ? DB::table($tblName)
                ->select(array_merge(["admno"], $columns, $aggregateCols))
                ->addSelect("students.name", "level_units.alias")
                ->join("students", "{$tblName}.admno", '=', 'students.adm_no')
                ->join("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id')
                ->where("{$tblName}.level_unit_id", $this->levelUnit->id)
                ->orderBy("level_unit_position")
                ->paginate(24)
            : collect([]);
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
        $aggregateCols = array("average", "total", "grade", "points", "level_unit_position", "level_position");

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

                $pgm = Grading::pointsGradeMap();

                $avgGrade = $pgm[$avgPoints];

                DB::table($tblName)
                ->updateOrInsert([
                    "admno" => $stuData->admno
                ], [
                    "average" => $avgScore,
                    "grade" => $avgGrade,
                    'points' => $avgPoints,
                    'total' => $totalScore
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

            $pgm = Grading::pointsGradeMap();

            $avgGrade = $pgm[$avgPoints];

            DB::table($tblName)
            ->updateOrInsert([
                "admno" => $stuData->admno
            ], [
                "average" => $avgScore,
                "grade" => $avgGrade,
                'points' => $avgPoints,
                'total' => $totalScore
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
                ->selectRaw("AVG(total) AS avg_total, AVG(points) avg_points")
                ->first();
            
            $avgTotal = number_format($data->avg_total, 2);
            $avgPoints = number_format($data->avg_points, 4);

            $pgm = Grading::pointsGradeMap();

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
     * Generate ranks based on the selected aggregate columns
     */
    public function generateRanks()
    {
        $data = $this->validate(['col' => ['nullable', 'string', Rule::in(array_keys($this->getRankColumns()))]]);

        $col = $data['col'] ?? 'total';

        try {

            $tblName = Str::slug($this->exam->shortname);

            // Get order records from the databas with the admno number as the primary key

            /** @var Collection */
            $data = DB::table($tblName)
                ->select(['admno', $col])
                ->where('level_unit_id', $this->levelUnit->id)
                ->orderBy($col, 'desc')
                ->get();

            $prevRank = -1;
            $currRank = -1;
            $prevVal = 0;
            $currVal = 0;

            foreach ($data as $key => $record) {

                if($key == 0) $currRank = 1;

                $currVal = $record->$col;

                if($key != 0){
                    if($prevVal == $currVal){
                        $currRank = $prevRank;
                    }
                }

                DB::table($tblName)->updateOrInsert(['admno' => $record->admno],[
                    'level_unit_position' => $currRank
                ]);

                $prevVal = $currVal;

                $prevRank = $currRank;

                ++$currRank;
            }

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
}
