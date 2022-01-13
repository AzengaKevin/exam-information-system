<?php 

namespace App\Actions\Exam\Scores;

use App\Models\Exam;
use App\Models\Level;
use App\Models\Grading;
use App\Models\Subject;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

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

        } catch (\Exception $exception) {

            throw $exception;

        }        
    }
}
