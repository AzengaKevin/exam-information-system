<?php 

namespace App\Actions\Exam\Scores;

use App\Exceptions\InvalidConnectionDriverException;
use App\Models\Exam;
use App\Models\Level;
use App\Models\Grading;
use App\Models\Subject;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class CompleteUpload
{
    
    /**
     * Action to calculate totals for an exam subject scores incase the subject has segments
     * 
     * @param Exam $exam
     * @param Subject $subject
     * @param Level $level
     * @param LevelUnit $levelUnit
     * 
     * @return bool
     */
    public static function calculateTotals(Exam $exam, Subject $subject, ?Level $level, ?LevelUnit $levelUnit) : bool
    {
        try {
    
            $tblName = Str::slug($exam->shortname);
    
            $col = $subject->shortname;

            $grading = Grading::first();

            $values = $grading->values;

            /** @var array */
            $segments = $subject->segments;

            /** @todo Check if all segments are filled */

            $grandTotal = array_reduce(array_values($segments), fn($prevSum, $currItem) => intval($prevSum) + intval($currItem), 0);

            $query = DB::table($tblName)->select("student_id", "{$col}");
    
            if(!is_null($level)) $query->where("level_id", $level->id);
    
            if(!is_null($levelUnit)) $query->where('level_unit_id', $levelUnit->id);
    
            /** @var Collection */
            $data = $query->get();
            
            $data->each(function($studentData) use($grandTotal, $segments, $tblName, $col, $values){

                $score = json_decode($studentData->$col);

                $total = 0;

                foreach ($segments as $key => $value) {
                    $total += intval($score->$key);
                }

                $percentScore = (floatval($total)/$grandTotal) * 100.0;

                $percentScore = intval($percentScore);
                $grade = null;
                $points = null;

                foreach ($values as $value) {
                    if($percentScore >= $value['min'] && $percentScore <= $value['max']){
                        $grade = $value['grade'];
                        $points = $value['points'];
                        break;
                    }
                }

                DB::update("UPDATE `$tblName` SET `$col` = JSON_SET(`$col`, \"$.score\", {$percentScore}, \"$.grade\", '{$grade}', \"$.points\", {$points}) WHERE student_id = {$studentData->student_id}");
                
            });
            
            return true;

        } catch (\Exception $exception) {

            throw $exception;

        }        
        
    }

    /**
     * Calculate deviations from the related deviation exam if any
     * @param Exam $exam - To calculate the deviation
     * @param Subject $subject - The secified subject
     * @param Level $level - Level to calculate the deviations for
     * @param LevelUnit $levelUnit - LevelUnit to calculate the deviations for
     * 
     * @return bool - Whether the deviations have been calculated or not
     */
    public static function calculateDeviations(Exam $exam, Subject $subject, ?Level $level, ?LevelUnit $levelUnit) : bool
    {
        /** @var Exam */
        $deviationExam = $exam->deviationExam;

        if(is_null($deviationExam)) return false;

        $dbDriver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if ($dbDriver == 'mysql') {
        
            $examTblName = Str::slug($exam->shortname);
    
            $devExamTblName = Str::slug($deviationExam->shortname);
        
            $col = $subject->shortname;
    
            $query = DB::table($examTblName,'cet')
                ->selectRaw("cet.student_id, (CAST(JSON_UNQUOTE(JSON_EXTRACT(cet.$col,\"$.score\")) AS SIGNED) - CAST(JSON_UNQUOTE(JSON_EXTRACT(`$devExamTblName`.$col,\"$.score\")) AS SIGNED)) AS dev")
                ->leftJoin($devExamTblName, 'cet.student_id', '=', "{$devExamTblName}.student_id");
    
            if(!is_null($level)) $query->where("cet.level_id", $level->id);
    
            if(!is_null($levelUnit)) $query->where('cet.level_unit_id', $levelUnit->id);            
        
            /** @var Collection */
            $data = $query->orderBy("dev", 'desc')->get();

            DB::transaction(function() use($data, $examTblName, $col){

                $data->each(function($item, $key) use ($examTblName, $col){
                    
                    $rank = $key + 1;
        
                    $deviation = $item->dev ?? 0;
        
                    DB::update("UPDATE `$examTblName` SET `$col` = JSON_SET(`$col`, \"$.dev\", $deviation, \"$.dev_rank\", $rank) WHERE student_id = {$item->student_id}");
        
                });
            });
    
            return true;
            
        }else{

            throw new InvalidConnectionDriverException("Only MySQL driver works well in such case");
        }
        
        return false;
    }

    /**
     * Action to generate ranks for an exam subject
     * 
     * @param Exam $exam
     * @param Subject $subject
     * @param Level $level
     * @param LevelUnit $levelUnit
     */
    public static function rank(Exam $exam, Subject $subject, ?Level $level, ?LevelUnit $levelUnit)
    {
        try {
    
            $dbDriver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

            if ($dbDriver == 'mysql') {

                $tblName = Str::slug($exam->shortname);
        
                $col = $subject->shortname;
                
                /** @var Collection */
                $query = DB::table($tblName)->selectRaw("student_id, CAST(JSON_UNQUOTE(JSON_EXTRACT($col,\"$.score\")) AS UNSIGNED) AS score");
        
                if(!is_null($level)) $query->where("level_id", $level->id);
        
                if(!is_null($levelUnit)) $query->where('level_unit_id', $levelUnit->id);
        
                /** @var Collection */
                $data = $query->orderBy("score", 'desc')->get();
        
                // Get the total records count
                $total = $data->count();
        
                $data->each(function($item, $key) use ($tblName, $col, $total){
    
                    $rank = $key + 1;
        
                    DB::update("UPDATE `$tblName` SET `$col` = JSON_SET(`$col`, \"$.rank\", $rank, \"$.total\", $total) WHERE student_id = {$item->student_id}");
    
                });
                
            } else {

                throw new InvalidConnectionDriverException("Only MySQL driver works well in such case");
                
            }
            

        } catch (\Exception $exception) {

            throw $exception;

        }        
    }
}
