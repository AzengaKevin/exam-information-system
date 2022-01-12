<?php 

namespace App\Actions\Exam\Scores;

use App\Models\Exam;
use App\Models\Level;
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
     */
    public static function calculateTotals(Exam $exam, Subject $subject, ?Level $level, ?LevelUnit $levelUnit)
    {
        
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
