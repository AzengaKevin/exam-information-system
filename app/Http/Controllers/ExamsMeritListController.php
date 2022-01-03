<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Level;
use App\Models\LevelUnit;
use App\Settings\GeneralSettings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Settings\SystemSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ExamsMeritListController extends Controller
{
    /**
     * Composes the required merit list and returns|downloads based on the request parameters
     * 
     * @param Request $request
     * @param Exam $exam
     */
    public function download(Request $request, Exam $exam, SystemSettings $systemSettings,
     GeneralSettings $generalSettings)
    {
        $levelId = $request->get('level');
        $levelUnitId = $request->get('level-unit');

        try {

            /** @var Level */
            $level = Level::find($levelId);

            /** @var LevelUnit */
            $levelUnit = LevelUnit::find($levelUnitId);
                    
            // Get the table name
            $tblName = Str::slug($exam->shortname);

            /** @var array */
            $columns = $exam->subjects->pluck("shortname")->toArray();

            /** @var array */
            $aggregateCols = $this->getAggregateColumns($systemSettings);

            /** @var array */
            $extraCols = array("admno", "name", "level");

            if ($systemSettings->school_has_streams) array_push($extraCols, "stream");

            if(Schema::hasTable($tblName)){

                $query = DB::table($tblName)
                    ->select(array_merge(["admno"], $columns, $aggregateCols))
                    ->addSelect("students.name", "level_units.alias as stream", "levels.name as level")
                    ->join("students", "{$tblName}.admno", '=', 'students.adm_no')
                    ->leftJoin("level_units", "{$tblName}.level_unit_id", '=', 'level_units.id')
                    ->leftJoin("levels", "{$tblName}.level_id", '=', 'levels.id')
                    ->orderBy($this->orderBy ?? 'op');
                
                    
                if (!empty($level)) $query->where("{$tblName}.level_id", $level->id);
                
                if (!empty($levelUnit)) $query->where("{$tblName}.level_unit_id", $levelUnit->id);
                    
                $data = $query->get();

                $pdf = \PDF::loadView("printouts.exams.merit-list",  [
                    'exam' => $exam,
                    'level' => $level,
                    'levelUnit' => $levelUnit,
                    'data' => $data,
                    'cols' => array_merge($extraCols, $columns, $aggregateCols),
                    'subjectCols' => $columns,
                    'systemSettings' => $systemSettings,
                    'generalSettings' => $generalSettings
                ]);

                $pdf->setPaper('A4', 'landscape');
                
                $filename = !is_null($level) 
                    ? Str::slug("{$exam->shortname}-{$level->name}")
                    : Str::slug("{$exam->shortname}-{$levelUnit->alias}");

                return $pdf->download("{$filename}.pdf");

            }

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            abort(404);
            
        }
        
    }

    /**
     * Get appropriate level columns
     * 
     * @return array
     */
    public function getAggregateColumns(SystemSettings $systemSettings) : array
    {
        $cols = array("mm", "tm", "op");

        if($systemSettings->school_level == 'secondary'){
            array_push($cols, "mg", "mp", "tp");
        }

        if ($systemSettings->school_has_streams) {
            array_push($cols, "sp");
        }

        return $cols;
    }    
}
