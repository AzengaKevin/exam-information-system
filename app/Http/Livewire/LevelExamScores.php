<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Level;
use App\Models\Grading;
use App\Settings\SystemSettings;
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

    public function mount(Exam $exam, Level $level)
    {
        $this->exam = $exam;
        $this->level = $level;
    }

    public function render()
    {
        return view('livewire.level-exam-scores', [
            'data' => $this->getResults(),
            'cols' => $this->getColumns(),
            'subjectCols' => $this->getSubjectColumns(),
            'columns' => $this->getRankColumns()
        ]);
    }

    public function getResults()
    {
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $columns = $this->getSubjectColumns();

        /** @var array */
        $aggregateCols = $this->getAggregateColumns();

        return Schema::hasTable($tblName)
            ? DB::table($tblName)
                ->select(array_merge(["admno", "levels.name AS level", "students.name"], $columns, $aggregateCols))
                ->join("students", "{$tblName}.student_id", '=', 'students.id')
                ->join("levels", "{$tblName}.level_id", '=', 'levels.id')
                ->where("{$tblName}.level_id", $this->level->id)
                ->orderBy('op')
                ->paginate(24)
            : collect([]);
    }

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
            
            $cols = $this->exam->subjects->pluck("shortname")->toArray();
    
            $tblName = Str::slug($this->exam->shortname);
    
            /** @var Collection */
            $data = DB::table($tblName)
                ->where("level_id", $this->level->id)
                ->select(array_merge(["student_id"], $cols))->get();
    
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
                    "student_id" => $stuData->student_id
                ], [
                    "mm" => $avgScore,
                    "mg" => $avgGrade,
                    'mp' => $avgPoints,
                    'tp' => $totalPoints,
                    'tm' => $totalScore
                ]);
            });

            session()->flash('status', 'Aggregates generated for all level students');
    
            $this->emit('hide-generate-aggregates-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred');

            $this->emit('hide-generate-aggregates-modal');
            
        }
        
    }

    /**
     * Publishing general level average score and average points
     */
    public function publishLevelScores()
    {
        try {

            $tblName = Str::slug($this->exam->shortname);

            $data = DB::table($tblName)
                ->where("level_id", $this->level->id)
                ->selectRaw("AVG(tm) AS avg_total, AVG(mp) avg_points")
                ->first();
        
            if (!is_null($data->avg_total) && !is_null($data->avg_points)) {
                
                $avgTotal = number_format($data->avg_total, 2);
                $avgPoints = number_format($data->avg_points, 4);
    
                $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');
    
                $avgGrade = $pgm[intval(round($avgPoints))] ?? 'P';
    
                $this->exam->levels()->syncWithoutDetaching([
                    $this->level->id => [
                        "points" => $avgPoints,
                        "grade" => $avgGrade,
                        "average" => $avgTotal
                    ]
                ]);

                session()->flash('status', 'Your class scores have been successfully published, you can republish the scores incase of any changes');
            }

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
     * Publish Level Students Grade Distribution
     */
    public function publishLevelGradeDistribution()
    {
        try {

            $tblName = Str::slug($this->exam->shortname);

            /** @var Collection */
            $data = DB::table($tblName)
                ->where('level_id', $this->level->id)
                ->selectRaw("mg, COUNT(mg) AS grade_count")
                ->groupBy('mg')
                ->get()
                ->pluck('grade_count', 'mg');

            if ($data->count()) {

                DB::beginTransaction();
    
                foreach (Grading::gradeOptions() as $grade) {

                    DB::table('exam_level_grade_distribution')
                        ->updateOrInsert([
                            'exam_id' => $this->exam->id,
                            'level_id' => $this->level->id,
                            'grade' => $grade,
                        ],['grade_count' => $data[$grade] ?? 0]);
                }
    
                DB::commit();
    
                session()->flash('status', 'Level grade distribution has been successfully published');
            }

            $this->emit('hide-publish-level-grade-dist-modal');
            
        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred');

            $this->emit('hide-publish-level-grade-dist-modal');
            
        }
        
    }

    /**
     * Publish level subject performance
     */
    public function publishLevelSubjectPerformance()
    {

        try {

            $tblName = Str::slug($this->exam->shortname);

            DB::beginTransaction();

            $atLeastASubjectPublished = false;

            foreach ($this->exam->subjects as $subject) {

                $col = $subject->shortname;

                $data = DB::table($tblName)
                    ->selectRaw("AVG(JSON_UNQUOTE(JSON_EXTRACT($col, \"$.points\"))) AS avg_points, AVG(JSON_UNQUOTE(JSON_EXTRACT($col, \"$.score\"))) AS avg_score")
                    ->where('level_id', $this->level->id)
                    ->whereNotNull($col)
                    ->first();

                if (!is_null($data->avg_points) && !is_null($data->avg_score)) {

                    $atLeastASubjectPublished = true;
                    
                    $avgTotal = number_format($data->avg_score, 2);
                    $avgPoints = number_format($data->avg_points, 4);
    
                    $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');
    
                    $avgGrade = $pgm[intval(round($avgPoints))];
    
                    DB::table('exam_level_subject_performance')
                        ->updateOrInsert([
                            'exam_id' => $this->exam->id,
                            'level_id' => $this->level->id,
                            'subject_id' => $subject->id
                        ], [
                            'average' => $avgTotal,
                            'points' => $avgPoints,
                            'grade' => $avgGrade
                        ]);
                }

            }

            DB::commit();

            if ($atLeastASubjectPublished) {
                session()->flash('status', 'Level subject performance has been successfully published');
            }

            $this->emit('hide-publish-subjects-performance-modal');
            
        } catch (\Exception $exception) {

            DB::rollBack();

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred');

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

            $tblName = Str::slug($this->exam->shortname);

            // Get order records from the databas with the admno number as the primary key

            /** @var Collection */
            $data = DB::table($tblName)
                ->select(['student_id', $col])
                ->where('level_id', $this->level->id)
                ->orderBy($col, 'desc')
                ->get();

            $data->each(function($item, $key) use($tblName) {

                $rank = $key + 1;
    
                DB::table($tblName)->updateOrInsert(['student_id' => $item->student_id],['op' => $rank]);

            });

            $this->emit('hide-rank-class-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred while trying to rank level students');

            $this->emit('hide-rank-class-modal');
        }   
        
    }

    /**
     * Publish Level Students Exam Results
     */
    public function publishStudentResults()
    {
        $tblName = Str::slug($this->exam->shortname);

        try {

            /** @var Collection */
            $data = DB::table($tblName)->select(array_merge(["students.id"], $this->getAllAgregateColumns()))
                ->join("students", "{$tblName}.student_id", '=', 'students.id')
                ->where("{$tblName}.level_id", $this->level->id)
                ->get();

            if ($data->count()) {
                
                $data->each(function($item){
    
                    DB::table('exam_student')
                        ->updateOrInsert([
                            'exam_id' => $this->exam->id,
                            'student_id' => $item->id
                        ], [
                            'mm' => $item->mm,
                            'tm' => $item->tm,
                            'mp' => $item->mp,
                            'tp' => $item->tp,
                            'mg' => $item->mg,
                            'sp' => $item->sp ?? null,
                            'op' => $item->op
                        ]);
    
                });
    
                session()->flash('status', "{$this->level->name} - {$this->exam->name} results published");
            }

            $this->emit('hide-publish-students-results-modal');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal error occurred while trying to publish students results');

            $this->emit('hide-publish-students-results-modal');

        }
    }
}
