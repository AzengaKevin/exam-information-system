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
            
            /** @var Subject */
            $subject = Subject::find(intval($request->get('subject')));

            /** @var LevelUnit */
            $levelUnit = LevelUnit::find(intval($request->get('level-unit')));

            /** @var Level */
            $level = Level::find(intval($request->get('level')));

            // Compute the proper title
            $title = "{$exam->name} Analysis";

            if($subject) $title = "Upload {$exam->name} - {$levelUnit->alias} - {$subject->name} Scores";
            elseif($levelUnit) $title = "{$levelUnit->alias} Scores Management";
            elseif($level) $title = "{$exam->name} - {$level->name} - Analysis";

            // Load level grade distribution
            $levelGradeDistribution = collect();

            $classScores = array();

            if($level){

                $levelGradeDistribution = DB::table('exam_level_grade_distribution')
                    ->where('level_id', $level->id)
                    ->select(['grade', 'grade_count'])
                    ->get(['grade', 'grade_count'])
                    ->pluck('grade_count', 'grade')
                    ->toArray();

                $levelUnits = $level->levelUnits()->with('stream')->get();

                $pointsData = DB::table('level_units')
                    ->select(['level_units.id', 'exam_level_unit.points'])
                    ->leftJoin('exam_level_unit', 'level_units.id', '=', 'exam_level_unit.level_unit_id')
                    ->where('level_units.level_id', $level->id)
                    ->where('exam_level_unit.exam_id', $exam->id)
                    ->get(['id', 'points'])
                    ->pluck('points', 'id')
                    ->toArray();
                
                $classScores = array();
        
                foreach ($levelUnits as $levelUnit) {
                    $classScores[$levelUnit->stream->slug] = $pointsData[$levelUnit->id] ?? 0;
                }

            }

            return view('exams.analysis.index', [
                'exam' => $exam,
                'level' => $level,
                'title' => $title,
                'levelGradeDistribution' => $levelGradeDistribution,
                'classScores' => $classScores
            ]);
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);

            abort(500, 'A fatal server error occurred');
            
        }

        
    }
}
