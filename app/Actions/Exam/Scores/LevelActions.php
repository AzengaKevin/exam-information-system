<?php

namespace App\Actions\Exam\Scores;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Level;
use App\Models\Grading;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\InvalidConnectionDriverException;

class LevelActions
{
    /**
     * Generate students aggregates
     * 
     * @param Exam $exam
     * @param Level $level
     */
    public static function generateAggregates(Exam $exam, Level $level)
    {
        if ($exam->deviationExam) {

            self::generateAggregatesWithDeviations($exam, $level);

        }else{

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
        
    }

    /**
     * Generate students aggregates (with deviations)
     * 
     * @param Exam $exam
     * @param Level $level
     */
    public static function generateAggregatesWithDeviations(Exam $exam, Level $level)
    {
        try {

            $subjectCols = $exam->subjects->pluck("shortname")->toArray();
    
            $examTblName = Str::slug($exam->shortname);

            $cols = array_map(fn($col) => "{$examTblName}.{$col}", $subjectCols);
            
            $devExamTblName = Str::slug($exam->deviationExam->shortname);
    
            /** @var Collection */
            $data = DB::table($examTblName)
                ->leftJoin($devExamTblName, "{$examTblName}.student_id", "=", "{$devExamTblName}.student_id")
                ->where("$examTblName.level_id", $level->id)
                ->select(array_merge(["$examTblName.student_id"], $cols, ["$devExamTblName.mm AS prev_mm", "$devExamTblName.tm AS prev_tm", "$devExamTblName.tp AS prev_tp", "$devExamTblName.mp AS prev_mp"]))->get();
    
            $data->each(function($stuData) use($examTblName, $subjectCols){
                $prevTm = $stuData->prev_tm;
                $prevTp = $stuData->prev_tp;
                $prevMp = $stuData->prev_mp;
                $prevMm = $stuData->prev_mm;
                $totalScore = 0;
                $totalPoints = 0;
                $populatedCols = 0;
    
                foreach ($subjectCols as $col) {
    
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
    
                DB::table($examTblName)
                ->updateOrInsert([
                    "student_id" => $stuData->student_id
                ], [
                    "mm" => $avgScore,
                    "mg" => $avgGrade,
                    'mp' => $avgPoints,
                    'tp' => $totalPoints,
                    'tm' => $totalScore,
                    'mmd' => ($avgScore - $prevMm),
                    'tmd' => ($totalScore - $prevTm),
                    'tpd' => ($totalPoints - $prevTp),
                    'mpd' => ($avgPoints - $prevMp),
                    'mpd' => ($prevMp - $avgPoints)
                ]);
            });

        } catch (\Exception $exception) {
            
            throw $exception;
            
        }
    }

    /**
     * Renerate ranks for the whole class
     * 
     * @param Exam $exam
     * @param Level $level
     */
    public static function generateRanks(Exam $exam, Level $level, string $col = 'tm')
    {

        try {

            $tblName = Str::slug($exam->shortname);

            // Get order records from the database with the admno number as the primary key

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
     * Compute level grade distribution and save it to the database
     * 
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
     * Publish level scores for the specified exam (Deviations included)
     * 
     * @param Exam $exam
     * @param Level $level
     */
    public static function publishScores(Exam $exam, Level $level)
    {

        /** @var Exam */
        $deviationExam = $exam->deviationExam;

        $levelWithPreviousScores = null;

        if ($deviationExam) {            
            $levelWithPreviousScores = $deviationExam->levels()
                ->where('levels.id', $level->id)
                ->first();
        }

        try {

            $tblName = Str::slug($exam->shortname);

            $data = DB::table($tblName)
                ->where("level_id", $level->id)
                ->selectRaw("AVG(tm) AS avg_total, AVG(mp) avg_points")
                ->first();
        
            if (!is_null($data->avg_total) && !is_null($data->avg_points)) {
                
                $avgTotal = number_format($data->avg_total, 2);
                $avgPoints = number_format($data->avg_points, 4);

                $previousAvgTotal = optional(optional($levelWithPreviousScores)->pivot)->average;
                $previousAvgPoints = optional(optional($levelWithPreviousScores)->pivot)->points;
    
                $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');
    
                $avgGrade = $pgm[intval(round($avgPoints))] ?? 'P';
    
                $exam->levels()->syncWithoutDetaching([
                    $level->id => [
                        "points" => $avgPoints,
                        "grade" => $avgGrade,
                        "average" => $avgTotal,
                        "points_deviation" => !is_null($previousAvgPoints) ? ($avgPoints - $previousAvgPoints) : 0,
                        "average_deviation" => !is_null($previousAvgTotal) ? ($avgTotal - $previousAvgTotal) : 0
                    ]
                ]);

            }

        } catch (\Exception $exception) {
            
            throw $exception;

        }
        
    }
    
    /**
     * Publish level subject performance, (deviations included)
     * 
     * @param Exam $exam
     * @param Level $level
     * 
     * @return bool
     */
    public static function publishSubjectPerformance(Exam $exam, Level $level) : bool
    {

        try {

            $tblName = Str::slug($exam->shortname);

            $subjectsWithPrevPerformance = collect([]);

            /** @var Exam */
            $deviationExam  = $exam->deviationExam;

            if ($deviationExam) {
                $subjectsWithPrevPerformance = $deviationExam->levelSubjectPerformance()
                    ->wherePivot('level_id', $level->id)
                    ->get();
            }

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

                    $prevAvgTotal = optional(optional($subjectsWithPrevPerformance->where('id', $subject->id)->first())->pivot)->average;
                    $prevAvgPoints = optional(optional($subjectsWithPrevPerformance->where('id', $subject->id)->first())->pivot)->points;
    
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
                            'average_deviation' => !empty($prevAvgTotal) ? ($avgTotal - $prevAvgTotal) : null,
                            'points_deviation' => !empty($prevAvgPoints) ? ($avgPoints - $prevAvgPoints) : null,
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
     * Publish students results at the level group (More appropriate if the school has no streams)
     * 
     * @param Exam $exam
     * @param Level $level
     */
    public static function publishStudentResults(Exam $exam, Level $level)
    {
        
        try {
            
            $tblName = Str::slug($exam->shortname);

            $aggregateColums = ["mm", "tm", "op", "mg", "mp", "tp", "sp", "mmd", "tmd", "tpd", "mpd"];

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
                            'op' => $item->op,
                            'mmd' => $item->mmd,
                            'tmd' => $item->tmd,
                            'tpd' => $item->tpd,
                            'mpd' => $item->mpd
                        ]);
    
                });
                
            }

        } catch (\Exception $exception) {
            
            throw $exception;

        }
        
    }

    /**
     * Publishing exam top students per subject
     * 
     * @param Exam $exam - the exam to publish
     * @param Level $level
     * @param int $howMany
     */
    public static function publishExamTopStudentsPerSubject(Exam $exam, Level $level, int $howMany = 3)
    {
        // Check the database driver
        $dbDriver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if(strtolower($dbDriver) == 'mysql'){
            
            DB::transaction(function() use($exam, $level, $howMany){

                $examTblName = Str::slug($exam->shortname);

                $exam->subjects->each(function($subject) use($exam, $examTblName, $howMany, $level){
    
                    $subjectCol = $subject->shortname;
                    $subjectId = $subject->id;

                    DB::table('exam_level_top_students_per_subject')
                        ->where([
                            ['exam_id' , $exam->id],
                            ['level_id' , $level->id],
                            ['subject_id' , $subjectId]
                        ])->delete();

    
                    /** @var Collection */
                    $data = DB::table($examTblName)
                        ->selectRaw("student_id, CAST(JSON_UNQUOTE(JSON_EXTRACT({$subjectCol}, \"$.score\")) AS UNSIGNED) AS score, JSON_UNQUOTE(JSON_EXTRACT({$subjectCol}, \"$.grade\")) AS grade")
                        ->where('level_id', $level->id)
                        ->orderBy("score", 'desc')
                        ->limit($howMany)
                        ->get();
    
                    $data->each(function($item) use($exam, $level, $subjectId){
                        DB::table('exam_level_top_students_per_subject')
                            ->updateOrInsert([
                                'exam_id' => $exam->id,
                                'level_id' => $level->id,
                                'subject_id' => $subjectId,
                                'student_id' => $item->student_id
                            ],[
                                'score' => $item->score,
                                'grade' => $item->grade
                            ]);
                    });
                });

            });

            
        }else{

            throw new InvalidConnectionDriverException("MySQL, will the best companion in this");

        }
        
    }
    
}
