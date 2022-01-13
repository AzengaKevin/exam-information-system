<?php

namespace App\Http\Livewire;

use App\Actions\Exam\Scores\LevelActions;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Level;
use App\Models\Grading;
use App\Settings\SystemSettings;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LevelExamScores extends Component
{
    public Exam $exam;
    public Level $level;

    public $col;

    /**
     * Lifecylce method that runs once when the component is mounting
     * @param Exam $exam
     * @param Level $level
     */
    public function mount(Exam $exam, Level $level)
    {
        $this->exam = $exam;
        $this->level = $level;
    }

    /**
     * Render and re-render the level-exam-scores component
     * @return View
     */
    public function render()
    {
        return view('livewire.level-exam-scores', [
            'data' => $this->getResults(),
            'cols' => $this->getColumns(),
            'subjectCols' => $this->getSubjectColumns(),
            'columns' => $this->getRankColumns()
        ]);
    }

    /**
     * Get paginated results of the the current level in the current exam
     * @return Paginator
     */
    public function getResults()
    {
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $columns = $this->getSubjectColumns();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        if (Schema::hasTable($tblName)) {

            return DB::table($tblName)
                ->select(array_merge(["levels.name AS level", "students.name"], $columns, $aggregateCols))
                ->join("students", "{$tblName}.student_id", '=', 'students.id')
                ->join("levels", "{$tblName}.level_id", '=', 'levels.id')
                ->where("{$tblName}.level_id", $this->level->id)
                ->orderBy('op')
                ->paginate(24)->withQueryString();

        }else{

            return new Paginator([], 24);

        }
    }

    /**
     * Get exam subject columns
     * 
     * @return array
     */
    public function getSubjectColumns(): array
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
     * All aggregate columns irregardless of settings
     * 
     * @return array
     */
    public function getAllAgregateColumns(): array
    {
        return ["mm", "tm", "op", "mg", "mp", "tp", "sp"];
    }

    /**
     * Get all the coluns that have been fetched and relevation for showing the data
     * 
     * @return array
     */
    public function getColumns()
    {
        /** @var array */
        $columns = $this->getSubjectColumns();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        /** @var array */
        $studentLevelCols = array("name", "level");

        return array_merge($studentLevelCols, $columns, $aggregateCols);;
    }

    /**
     * Generate aggregates (mm, tm, mg, tp, sp, op) for the whole level students
     */
    public function generateBulkLevelAggregates()
    {
        try {
            
            LevelActions::generateAggregates($this->exam, $this->level);

            session()->flash('status', 'Aggregates generated for all level students');
    
            $this->emit('hide-generate-aggregates-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-generate-aggregates-modal');
            
        }
        
    }

    /**
     * Publishing general level average score and average points
     */
    public function publishLevelScores()
    {
        try {

            /** @var SystemSettings */
            $systemSettings = app(SystemSettings::class);

            if(!$systemSettings->school_has_streams) LevelActions::generateAggregates($this->exam, $this->level);
            LevelActions::generateRanks($this->exam, $this->level);
            LevelActions::publishGradeDistribution($this->exam, $this->level);
            LevelActions::publishScores($this->exam, $this->level);
            LevelActions::publishSubjectPerformance($this->exam, $this->level);
            LevelActions::publishStudentResults($this->exam, $this->level);
            
            LevelActions::publishScores($this->exam, $this->level);

            session()->flash('status', "Level scores have successfully published");

            $this->emit('hide-publish-class-scores-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-publish-class-scores-modal');

        }
        
    }

    /**
     * Publish Level Students Grade Distribution
     */
    public function publishLevelGradeDistribution()
    {
        try {
            
            LevelActions::publishGradeDistribution($this->exam, $this->level);

            $this->emit('hide-publish-level-grade-dist-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-publish-level-grade-dist-modal');
            
        }
        
    }

    /**
     * Publish level subject performance
     */
    public function publishLevelSubjectPerformance()
    {
        try {

            $atLeastASubjectPublished = LevelActions::publishSubjectPerformance($this->exam, $this->level);

            if ($atLeastASubjectPublished) session()->flash('status', 'Level subject performance has been successfully published');

            $this->emit('hide-publish-subjects-performance-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-publish-subjects-performance-modal');
            
        }
    }

    /**
     * Columns that can be used for ranking students
     */
    public function getRankColumns() : array
    {
        return [
            'mp' => 'Aggregate Points',
            'tm' => 'Total Score'
        ];
    }    


    /**
     * Generate ranks based on the selected aggregate columns
     */
    public function generateRanks()
    {
        $data = $this->validate(['col' => ['nullable', 'string', Rule::in(array_keys($this->getRankColumns()))]]);

        $col = $data['col'] ?? 'tm';

        try {
            
            LevelActions::generateRanks($this->exam, $this->level, $col);

            session()->flash("success", "{$this->level->name} ranks have been succefully generated");

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
     * Publish Level Students Exam Results
     */
    public function publishStudentResults()
    {
        
        try {
            
            LevelActions::publishStudentResults($this->exam, $this->level);

            $this->emit('hide-publish-students-results-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-publish-students-results-modal');

        }
    }
}
