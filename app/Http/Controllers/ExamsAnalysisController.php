<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Level;
use App\Models\Subject;
use App\Models\LevelUnit;
use Illuminate\Http\Request;
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
}
