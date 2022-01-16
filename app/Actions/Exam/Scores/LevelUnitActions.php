<?php

namespace App\Actions\Exam\Scores;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Grading;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class LevelUnitActions
{
    /**
     * Generate exam aggregates for a level unit
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     * 
     */
    public static function generateAggregates(Exam $exam, LevelUnit $levelUnit)
    {
        if ($exam->deviationExam) {

            self::generateAggregatesWithDeviations($exam, $levelUnit);

        }else{
            try {
                
                $cols = $exam->subjects->pluck("shortname")->toArray();
    
                $tblName = Str::slug($exam->shortname);
    
                /** @var Collection */
                $data = DB::table($tblName)
                    ->where("level_unit_id", $levelUnit->id)
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
     * Generate exam aggregates for a level unit (With deviations)
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     */
    public static function generateAggregatesWithDeviations(Exam $exam, LevelUnit $levelUnit)
    {
        try {
            $subjectCols = $exam->subjects->pluck("shortname")->toArray();
            
            $examTblName = Str::slug($exam->shortname);

            $cols = array_map(fn($col) => "{$examTblName}.{$col}", $subjectCols);
            
            $devExamTblName = Str::slug($exam->deviationExam->shortname);
    
            /** @var Collection */
            $data = DB::table($examTblName)
                ->leftJoin($devExamTblName, "{$examTblName}.student_id", "=", "{$devExamTblName}.student_id")
                ->where("$examTblName.level_unit_id", $levelUnit->id)
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
                    'mpd' => ($avgPoints - $prevMp)
                ]);
            });

        } catch (\Exception $exception) {
            
            throw $exception;
            
        }
        
    }

    /**
     * Generate ranks for a level-unit
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     * 
     */
    public static function generateRanks(Exam $exam, LevelUnit $levelUnit, string $col = 'tm')
    {
        try {

            $tblName = Str::slug($exam->shortname);

            // Get order records from the databas with the admno number as the primary key
            /** @var Collection */
            $data = DB::table($tblName)
                ->select(['student_id', $col])
                ->where('level_unit_id', $levelUnit->id)
                ->orderBy($col, 'desc')
                ->get();

            $data->each(function($item, $key) use($tblName){

                $rank = $key + 1;

                DB::table($tblName)->updateOrInsert(['student_id' => $item->student_id],['sp' => $rank]);
                
            });

        } catch (\Exception $exception) {

            throw $exception;

        }
        
    }

    /**
     * Publish grade distribution got the specified level unit in the specified exam
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     * 
     */
    public static function publishGradeDistribution(Exam $exam, LevelUnit $levelUnit)
    {
        
        try {
            
            $tblName = Str::slug($exam->shortname);

            /** @var Collection */
            $data = DB::table($tblName)
                ->where('level_unit_id', $levelUnit->id)
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
                            'exam_id' => $exam->id,
                            'level_unit_id' => $levelUnit->id,
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
     * Publishing subject performance for the specified level unit in the specified exam
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     * 
     * @return bool
     */
    public static function publishSubjectPerformance(Exam $exam, LevelUnit $levelUnit) : bool
    {

        try {

            $tblName = Str::slug($exam->shortname);

            $subjectsWithPreviousScores = collect([]);

            /** @var Exam */
            $deviationExam = $exam->deviationExam;

            if ($deviationExam) {
                $subjectsWithPreviousScores = $deviationExam->levelUnitSubjectPerformance()
                    ->wherePivot('level_unit_id', $levelUnit->id)
                    ->get();
            }

            DB::beginTransaction();

            $atLeastASubjectPublished = false;

            foreach ($exam->subjects as $subject) {

                $col = $subject->shortname;

                $data = DB::table($tblName)
                    ->selectRaw("AVG(JSON_UNQUOTE(JSON_EXTRACT($col, \"$.points\"))) AS avg_points, AVG(JSON_UNQUOTE(JSON_EXTRACT($col, \"$.score\"))) AS avg_score")
                    ->where('level_unit_id', $levelUnit->id)
                    ->whereNotNull($col)
                    ->first();

                if (!is_null($data->avg_points) && !is_null($data->avg_score)) {

                    $atLeastASubjectPublished = true;
                    
                    $avgTotal = number_format($data->avg_score, 2);
                    $avgPoints = number_format($data->avg_points, 4);
                    $prevAvgTotal = optional(optional($subjectsWithPreviousScores->where('id', $subject->id)->first())->pivot)->average;
                    $prevAvgPoints = optional(optional($subjectsWithPreviousScores->where('id', $subject->id)->first())->pivot)->points;
    
                    $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');
    
                    $avgGrade = $pgm[intval(round($avgPoints))];
    
                    DB::table('exam_level_unit_subject_performance')
                        ->updateOrInsert([
                            'exam_id' => $exam->id,
                            'level_unit_id' => $levelUnit->id,
                            'subject_id' => $subject->id
                        ], [
                            'average' => $avgTotal,
                            'points' => $avgPoints,
                            'grade' => $avgGrade,
                            'average_deviation' => !empty($prevAvgTotal) ? ($avgTotal - $prevAvgTotal) : null,
                            'points_deviation' => !empty($prevAvgPoints) ? ($avgPoints - $prevAvgPoints) : null,
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
     * Publish LevelUnit Exam Scores (With deviations included)
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     */
    public static function publishScores(Exam $exam, LevelUnit $levelUnit)
    {
        /** @var Exam */
        $deviationExam = $exam->deviationExam;

        $levelUnitWithPreviousScores = null;

        if($deviationExam){
            $levelUnitWithPreviousScores = $deviationExam->levelUnits()
                ->where('level_units.id', $levelUnit->id)
                ->first();
        }

        try {

            $tblName = Str::slug($exam->shortname);

            $data = DB::table($tblName)
                ->where("level_unit_id", $levelUnit->id)
                ->selectRaw("AVG(tm) AS avg_total, AVG(mp) avg_points")
                ->first();
            
            $avgTotal = number_format($data->avg_total, 2);
            $avgPoints = number_format($data->avg_points, 4);

            $previousAvgTotal = optional(optional($levelUnitWithPreviousScores)->pivot)->average;
            $previousAvgPoints = optional(optional($levelUnitWithPreviousScores)->pivot)->points;

            $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

            $avgGrade = $pgm[intval(round($avgPoints))];

            $exam->levelUnits()->syncWithoutDetaching([
                $levelUnit->id => [
                    "points" => $avgPoints,
                    "grade" => $avgGrade,
                    "average" => $avgTotal,
                    "points_deviation" => !is_null($previousAvgPoints) ? ($avgPoints - $previousAvgPoints) : 0,
                    "average_deviation" => !is_null($previousAvgTotal) ? ($avgTotal - $previousAvgTotal) : 0
                ]
            ]);

        } catch (\Exception $exception) {
            
            throw $exception;

        }
        
    }

    /**
     * Publish student results at level unit group (Deviations included)
     * 
     * @param Exam $exam
     * @param LevelUnit $level
     */
    public static function publishStudentResults(Exam $exam, LevelUnit $levelUnit)
    {
        
        try {
            
            $tblName = Str::slug($exam->shortname);

            $aggregateColums = ["mm", "tm", "op", "mg", "mp", "tp", "sp", "mmd", "tmd", "tpd", "mpd"];

            /** @var Collection */
            $data = DB::table($tblName)->select(array_merge(["students.id"], $aggregateColums))
                ->join("students", "{$tblName}.student_id", '=', 'students.id')
                ->where("{$tblName}.level_unit_id", $levelUnit->id)
                ->get();

            if ($data->count()) {
                
                $data->each(function($item) use($exam){
    
                    DB::table('exam_student')
                        ->updateOrInsert(['exam_id' => $exam->id,'student_id' => $item->id], [
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
}
