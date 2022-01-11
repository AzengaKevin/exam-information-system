<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Level;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Settings\SystemSettings;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamsAnalysisController extends Controller
{
    /**
     * Show General Exam Overview 
     * 
     * @param Request $request
     * @param Exam $exam
     * 
     */
    public function index(Request $request, Exam $exam)
    {
        try {
            
            /** @var LevelUnit */
            $levelUnit = LevelUnit::find(intval($request->get('level-unit')));

            /** @var Level */
            $level = Level::find(intval($request->get('level')));

            // Compute the proper title
            $title = "{$exam->name} Analysis";

            if($levelUnit) $title = "{$exam->name} - {$levelUnit->alias} - Analysis";
            elseif($level) $title = "{$exam->name} - {$level->name} - Analysis";

            return view('exams.analysis.index', [
                'exam' => $exam,
                'level' => $level,
                'levelUnit' => $levelUnit,
                'title' => $title,
            ]);
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);

        }
        
    }

    /**
     * Download part of the exam analysis using the specified request paramaters
     * 
     * @param Request $request
     * @param Exam $exam
     * @param SystemSttings $systemSettings
     * @param GeneralSettings $generalSettings
     */
    public function download(Request $request, Exam $exam, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {
        try {

            /** @var Level */
            $level = Level::find(intval($request->get('level')));

            // Compute the proper title
            $title = "{$exam->name} Analysis";

            if($level) $title = "{$exam->name} - {$level->name} - Analysis";

            $levelWithData = $exam->levels()->where('exam_level.level_id', $level->id)->first();

            $studentsCount = $exam->students()->where('students.level_id', $level->id)->count();

            $gradeDist = DB::table('exam_level_grade_distribution')
                ->where([['level_id', $level->id],['exam_id', $exam->id]])
                ->select(['grade', 'grade_count'])
                ->get(['grade', 'grade_count'])
                ->pluck('grade_count', 'grade')
                ->toArray();

            $pdf = \PDF::loadView('printouts.exams.analysis.report', [
                'exam' => $exam,
                'level' => $level,
                'levelWithData' => $levelWithData,
                'studentsCount' => $studentsCount,
                'gradeDist' => $gradeDist,
                'title' => $title,
                'systemSettings' => $systemSettings,
                'generalSettings' => $generalSettings
            ]);
                
            $filename = Str::slug("{$exam->shortname}-{$level->name}-analysis");

            return $pdf->download("{$filename}.pdf");
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            return back();
        }
        
    }
}
