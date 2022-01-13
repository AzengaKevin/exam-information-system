<?php

namespace App\Actions\Exam\Scores;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Level;
use App\Models\Grading;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LevelActions
{
    /**
     * @param Exam $exam
     * @param Level $level
     */
    public static function generateAggregates(Exam $exam, Level $level)
    {
        try {
            
            $cols = $exam->subjects->pluck("shortname")->toArray();
    
            $tblName = Str::slug($exam->shortname);
    
            /** @var Collection */
            $data = DB::table($tblName)
                ->where("level_id", $level->id)
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

        } catch (\Exception $exception) {
            
            throw $exception;
            
        }
        
    }

    /**
     * @param Exam $exam
     * @param Level $level
     */
    public static function generateRanks(Exam $exam, Level $level, string $col = 'tm')
    {

        try {

            $tblName = Str::slug($exam->shortname);

            // Get order records from the databas with the admno number as the primary key

            /** @var Collection */
            $data = DB::table($tblName)
                ->select(['student_id', $col])
                ->where('level_id', $level->id)
                ->orderBy($col, 'desc')
                ->get();

            $data->each(function($item, $key) use($tblName) {
                $rank = $key + 1;
                DB::table($tblName)->updateOrInsert(['student_id' => $item->student_id],['op' => $rank]);
            });

        } catch (\Exception $exception) {
            
            throw $exception;
        }
        
    }
    
    /**
     * @param Exam $exam
     * @param Level $level
     */
    public static function publishGradeDistribution(Exam $exam, Level $level)
    {
        try {

            $tblName = Str::slug($exam->shortname);

            /** @var Collection */
            $data = DB::table($tblName)
                ->where('level_id', $level->id)
                ->selectRaw("mg, COUNT(mg) AS grade_count")
                ->groupBy('mg')
                ->get()
                ->pluck('grade_count', 'mg');

            if ($data->count()) {

                DB::beginTransaction();
    
                foreach (Grading::gradeOptions() as $grade) {

                    DB::table('exam_level_grade_distribution')
                        ->updateOrInsert([
                            'exam_id' => $exam->id,
                            'level_id' => $level->id,
                            'grade' => $grade,
                        ],['grade_count' => $data[$grade] ?? 0]);
                }
    
                DB::commit();
    
            }

        } catch (\Exception $exception) {

            DB::rollBack();
            
            throw $exception;
            
        }

    }
    
    /**
     * @param Exam $exam
     * @param Level $level
     */
    public static function publishScores(Exam $exam, Level $level)
    {
        try {

            $tblName = Str::slug($exam->shortname);

            $data = DB::table($tblName)
                ->where("level_id", $level->id)
                ->selectRaw("AVG(tm) AS avg_total, AVG(mp) avg_points")
                ->first();
        
            if (!is_null($data->avg_total) && !is_null($data->avg_points)) {
                
                $avgTotal = number_format($data->avg_total, 2);
                $avgPoints = number_format($data->avg_points, 4);
    
                $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');
    
                $avgGrade = $pgm[intval(round($avgPoints))] ?? 'P';
    
                $exam->levels()->syncWithoutDetaching([
                    $level->id => [
                        "points" => $avgPoints,
                        "grade" => $avgGrade,
                        "average" => $avgTotal
                    ]
                ]);

            }

        } catch (\Exception $exception) {
            
            throw $exception;

        }
        
    }
    
    /**
     * @param Exam $exam
     * @param Level $level
     * 
     * @return bool
     */
    public static function publishSubjectPerformance(Exam $exam, Level $level) : bool
    {

        try {

            $tblName = Str::slug($exam->shortname);

            DB::beginTransaction();

            $atLeastASubjectPublished = false;

            foreach ($exam->subjects as $subject) {

                $col = $subject->shortname;

                $data = DB::table($tblName)
                    ->selectRaw("AVG(JSON_UNQUOTE(JSON_EXTRACT($col, \"$.points\"))) AS avg_points, AVG(JSON_UNQUOTE(JSON_EXTRACT($col, \"$.score\"))) AS avg_score")
                    ->where('level_id', $level->id)
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
                            'exam_id' => $exam->id,
                            'level_id' => $level->id,
                            'subject_id' => $subject->id
                        ], [
                            'average' => $avgTotal,
                            'points' => $avgPoints,
                            'grade' => $avgGrade
                        ]);
                }

            }

            DB::commit();

            return $atLeastASubjectPublished;

        } catch (\Exception $exception) {

            DB::rollBack();
            
            throw $exception;
            
        }
        
    }
    
    /**
     * @param Exam $exam
     * @param Level $level
     */
    public static function publishStudentResults(Exam $exam, Level $level)
    {
        
        try {
            
            $tblName = Str::slug($exam->shortname);

            $aggregateColums = ["mm", "tm", "op", "mg", "mp", "tp", "sp"];

            /** @var Collection */
            $data = DB::table($tblName)->select(array_merge(["students.id"], $aggregateColums))
                ->join("students", "{$tblName}.student_id", '=', 'students.id')
                ->where("{$tblName}.level_id", $level->id)
                ->get();

            if ($data->count()) {
                
                $data->each(function($item) use($exam){
    
                    DB::table('exam_student')
                        ->updateOrInsert([
                            'exam_id' => $exam->id,
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
                
            }

        } catch (\Exception $exception) {
            
            throw $exception;

        }
        
    }
    
}
