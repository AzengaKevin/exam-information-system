<?php

namespace App\Http\Livewire;

use App\Actions\Exam\Scores\LevelUnitActions;
use App\Exceptions\InvalidConnectionDriverException;
use App\Models\Exam;
use App\Models\Grade;
use Livewire\Component;
use App\Models\LevelUnit;
use App\Models\Student;
use App\Settings\SystemSettings;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LevelUnitExamScores extends Component
{
    /** @var Exam */
    public Exam $exam;

    public LevelUnit $levelUnit;

    public $student_id;
    public $name;

    public $col;

    /**
     * Lifecyle method that runs once when the component is mounting
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
     * Rendering and re-rendering a component when the state of the component changes
     * 
     * @return View
     */
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
     * 
     * @return array
     */
    public function getRankColumns() : array
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);
        
        $columns = array('tm' => 'Total Score');

        if($systemSettings->school_level === 'secondary') $columns['tp'] = 'Aggregate Points';
        
        return $columns;
    }

    /**
     * Get the level unit results data
     * 
     * @return Paginator
     */
    public function getResults()
    {
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $columns = $this->exam->subjects->pluck("shortname")->toArray();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        if (Schema::hasTable($tblName)) {

            return DB::table($tblName)
                ->select(array_merge(["student_id"], $columns, $aggregateCols))
                ->addSelect("students.name", "level_units.alias")
                ->join("students", "{$tblName}.student_id", '=', 'students.id')
                ->join("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id')
                ->where("{$tblName}.level_unit_id", $this->levelUnit->id)
                ->orderBy("sp")
                ->paginate(24)
                ->withQueryString();

        }else{

            return new Paginator([], 24);

        }
        
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
            
            LevelUnitActions::generateAggregates($this->exam, $this->levelUnit);

            session()->flash('status', 'Aggregates for the whole class successfully generated');

            $this->emit('hide-generate-scores-aggregates-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-generate-scores-aggregates-modal');
            
        }
        
    }

    /**
     * Triggers Javascript to show modal for generating aggregates
     * 
     * @param string $student_id
     */
    public function showGenerateAggregatesModal(string $student_id)
    {
        $this->student_id = $student_id;

        /** @var Student */
        $student = Student::findOrFail($this->student_id);

        $this->name = $student->name;

        $this->emit('show-generate-scores-aggregates-modal');
        
    }

    /**
     * Generate aggregates for a single scores table record i.e student
     */
    public function generateAggregates()
    {

        try {

            /** @var Student */
            $student = Student::findOrFail($this->student_id);

            $cols = $this->getSubjectColumns();

            $tblName = Str::slug($this->exam->shortname);

            $stuData = DB::table($tblName)
                ->where("level_unit_id", $this->levelUnit->id)
                ->where('student_id', $student->id)
                ->select(array_merge(["student_id"], $cols))->first();

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

            DB::table($tblName)->updateOrInsert(["student_id" => $stuData->student_id], [
                "mm" => $avgScore,
                "mg" => $avgGrade,
                'mp' => $avgPoints,
                'tp' => $totalPoints,
                'tm' => $totalScore
            ]);

            $this->reset(['student_id', 'name']);

            session()->flash('status', "Aggregates for {$student->name} class successfully generated");

            $this->emit('hide-generate-scores-aggregates-modal');   

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $this->reset(['student_id', 'name']);

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

            LevelUnitActions::generateAggregates($this->exam, $this->levelUnit);

            LevelUnitActions::generateRanks($this->exam, $this->levelUnit);

            LevelUnitActions::publishGradeDistribution($this->exam, $this->levelUnit);

            LevelUnitActions::publishSubjectPerformance($this->exam, $this->levelUnit);

            LevelUnitActions::publishScores($this->exam, $this->levelUnit);

            LevelUnitActions::publishStudentResults($this->exam, $this->levelUnit);

            session()->flash('status', "Class {$this->levelUnit->alias} scores have been successfully published, you can republish the scores incase of any changes");

            $this->emit('hide-publish-class-scores-modal');

        } catch (\Exception $exception) {

            if($exception instanceof InvalidConnectionDriverException){

                $this->addError('error', $exception->getMessage());

            }else{
                
                Log::error($exception->getMessage(), ['action' => __METHOD__]);
    
                $message = App::environment('local')
                    ? $exception->getMessage()
                    : "Could not publish, {$this->levelUnit->alias} scores";
    
                session()->flash('error', $message);
            }

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
            
            LevelUnitActions::generateRanks($this->exam, $this->levelUnit, $col);

            session()->flash('status', 'Student ranking operation completed successfully');

            $this->emit('hide-rank-class-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-rank-class-modal');
        }   
        
    }

    /**
     * Publish current exam level unit grade distribution
     */
    public function publishLevelUnitGradeDistribution()
    {
        
        try {

            LevelUnitActions::publishGradeDistribution($this->exam, $this->levelUnit);
            
            session()->flash('status', 'Level Unit grade distribution has been successfully published');

            $this->emit('hide-publish-level-unit-grade-dist-modal');

            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-publish-level-unit-grade-dist-modal');
            
        }
        
    }

    /**
     * Publish current level unit subject performance
     */
    public function publishLevelUnitSubjectPerformance()
    {

        try {

            $atLeastASubjectPublished = LevelUnitActions::publishSubjectPerformance($this->exam, $this->levelUnit);

            if ($atLeastASubjectPublished) {
                session()->flash('status', 'Level unit subject performance has been successfully published');
            }

            $this->emit('hide-publish-class-subjects-performance-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-publish-class-subjects-performance-modal');
            
        }
        
    }

    /**
     * Publish Level Students Exam Results
     */
    public function publishStudentResults()
    {
        
        try {
            
            LevelUnitActions::publishStudentResults($this->exam, $this->levelUnit);

            session('status', "{$this->levelUnit->alias} students results have been published");

            $this->emit('hide-publish-class-students-results-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-publish-class-students-results-modal');

        }
    }    
}
