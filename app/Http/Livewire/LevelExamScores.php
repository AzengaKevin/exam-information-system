<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Level;
use App\Models\Grading;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LevelExamScores extends Component
{
    public Exam $exam;
    public Level $level;

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
            'subjectCols' => $this->getSubjectColumns()
        ]);
    }

    public function getResults()
    {
        $tblName = Str::slug($this->exam->shortname);

        /** @var array */
        $columns = $this->getSubjectColumns();

        /** @var array */
        $aggregateCols = array("average", "total", "grade", "points", "level_unit_position", "level_position");

        return Schema::hasTable($tblName)
            ? DB::table($tblName)
                ->select(array_merge(["admno"], $columns, $aggregateCols))
                ->addSelect("students.name", "level_units.alias")
                ->join("students", "{$tblName}.admno", '=', 'students.adm_no')
                ->join("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id')
                ->where("{$tblName}.level_id", $this->level->id)
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

    public function publishLevelScores()
    {
        try {

            $tblName = Str::slug($this->exam->shortname);

            $data = DB::table($tblName)
                ->where("level_id", $this->level->id)
                ->selectRaw("AVG(total) AS avg_total, AVG(points) avg_points")
                ->first();
            
            $avgTotal = number_format($data->avg_total, 2);
            $avgPoints = number_format($data->avg_points, 4);

            $pgm = Grading::pointsGradeMap();

            $avgGrade = $pgm[intval(round($avgPoints))];

            $this->exam->levels()->syncWithoutDetaching([
                $this->level->id => [
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
     * Publish Level Students Grade Distribution
     */
    public function publishLevelGradeDistribution()
    {
        try {

            DB::beginTransaction();

            $tblName = Str::slug($this->exam->shortname);

            $data = DB::table($tblName)
                ->where('level_id', $this->level->id)
                ->selectRaw("grade, COUNT(grade) AS grade_count")
                ->groupBy('grade')
                ->get()
                ->pluck('grade_count', 'grade');
            
            DB::table('exam_level_grade_distribution')
                ->where([
                    'exam_id' => $this->exam->id,
                    'level_id' => $this->level->id,
                ])->delete();

            foreach (Grading::gradeOptions() as $grade) {
                $this->exam->levelGradesDist()->attach([
                    $this->level->id => [
                        'grade' => $grade,
                        'grade_count' => $data[$grade] ?? 0
                    ]
                ]);
            }

            DB::commit();

            session()->flash('status', 'Level grade distribution has been successfully published');

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

            DB::table('exam_level_subject_performance')
            ->where([
                'exam_id' => $this->exam->id,
                'level_id' => $this->level->id,
            ])->delete();

            foreach ($this->exam->subjects as $subject) {

                $col = $subject->shortname;

                $data = DB::table($tblName)
                    ->selectRaw("AVG(JSON_UNQUOTE(JSON_EXTRACT($col, \"$.points\"))) AS avg_points, AVG(JSON_UNQUOTE(JSON_EXTRACT($col, \"$.score\"))) AS avg_score")
                    ->where('level_id', $this->level->id)
                    ->whereNotNull($col)
                    ->first();

                $avgTotal = number_format($data->avg_score, 2);
                $avgPoints = number_format($data->avg_points, 4);

                $pgm = Grading::pointsGradeMap();

                $avgGrade = $pgm[intval(round($avgPoints))];
                
                $this->exam->levelSubjectPerformance()
                    ->attach([
                        $this->level->id => [
                            'subject_id' => $subject->id,
                            'average' => $avgTotal,
                            'points' => $avgPoints,
                            'grade' => $avgGrade
                        ]
                    ]);
            }

            DB::commit();

            session()->flash('status', 'Level subject performance has been successfully published');

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
}
