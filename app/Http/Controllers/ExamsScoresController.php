<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Responsibility;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\UpsertScoresRequest;
use App\Models\Grading;
use App\Models\Level;

class ExamsScoresController extends Controller
{

    /**
     * Show a Teacher all his classes that are enrolled in an exam and he/she is
     * supposed to upload the scores
     * 
     * @param Request $request
     * @param Exam $exam
     */
    public function index(Request $request, Exam $exam)
    {
        /** @var User */
        $user = $request->user();

        /** @var Teacher */
        $teacher = $user->authenticatable;

        /** @var Responsibility */
        $responsibility = Responsibility::firstOrCreate(['name' => 'Subject Teacher']);
        $classTeacherResponsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);
        $levelSupervisorResponsibility = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);

        $dosResponsibility = Responsibility::firstOrCreate(['name' => 'Director of Studies']);

        $responsibilities = $teacher->responsibilities()
            ->whereIn('responsibilities.id', [
                $responsibility->id, 
                $classTeacherResponsibility->id, 
                $levelSupervisorResponsibility->id
            ])->get();

        $levels = collect([]);

        $levelUnits = collect([]);

        if ($teacher->responsibilities()->where('responsibilities.id', $dosResponsibility->id)->exists()) {
            $levels = $exam->levels;

            $levelUnits = LevelUnit::whereIn('level_id', $exam->levels->pluck('id')->toArray())
                ->get();
        }

        return view('exams.scores.index', [
            'exam' => $exam,
            'responsibilities' => $responsibilities,
            'levels' => $levels,
            'levelUnits' => $levelUnits,
        ]);
        
    }

    /**
     * Show page for uploading students marks
     * 
     * @param Request $request
     * @param Exam $exam
     * 
     */
    public function create(Request $request, Exam $exam)
    {

        try {

            /** @var Subject */
            $subject = Subject::find(intval($request->get('subject')));

            /** @var LevelUnit */
            $levelUnit = LevelUnit::find(intval($request->get('level-unit')));

            /** @var Level */
            $level = Level::find(intval($request->get('level')));

            $gradings = Grading::all(['id', 'name']);

            // Get previous scores if available
            $scores = array();

            $title = "Manage Scores";

            if($subject) $title = "Upload {$exam->name} - {$levelUnit->alias} - {$subject->name} Scores";
            elseif($levelUnit) $title = "{$levelUnit->alias} Scores Management";
            elseif($level) $title = "{$level->name} Scores Management";

            if ($subject) {
                
                if (Schema::hasTable(Str::slug($exam->shortname))) {
    
                    if($subject){
    
                        $col = $subject->shortname;
        
                        /** @var Collection */
                        $data = DB::table(Str::slug($exam->shortname))
                            ->select('admno', $col)
                            ->where('level_unit_id', $levelUnit->id)
                            ->get();
                            
                        foreach ($data as $value) {
                            $scores[$value->admno] = optional(json_decode($value->$col))->score ?? null;
                        }
                        
                    }
                }

            }

            return view('exams.scores.create', [
                'subject' => $subject,
                'levelUnit' => $levelUnit,
                'level' => $level,
                'exam' => $exam,
                'scores' => $scores,
                'gradings' => $gradings,
                'title' => $title
            ]);

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);

            abort(500, 'A fatal server error occurred');
            
        }
    }

    /**
     * Record stores to the database
     */
    public function store(UpsertScoresRequest $request, Exam $exam)
    {
        $data = $request->validated();

        try {

            /** @var Subject */
            $subject = Subject::findOrFail(intval($request->get('subject')));

            /** @var LevelUnit */
            $levelUnit = LevelUnit::findOrFail(intval($request->get('level-unit')));

            $grading = Grading::find($data['grading_id'] ?? 1) ?? Grading::first();

            $values = $grading->values;

            foreach ($data["scores"] as $admno => $score) {

                $grade = null;
                $points = null;

                foreach ($values as $value) {

                    if($score >= $value['min'] && $score <= $value['max']){
                        $grade = $value['grade'];
                        $points = $value['points'];
                        break;
                    }

                }
                
                
                DB::table(Str::slug($exam->shortname))
                    ->updateOrInsert([
                        "admno" => $admno
                    ], [
                        $subject->shortname => json_encode([
                                'score' => $score,
                                'grade' => $grade,
                                'points' => $points,
                        ]),
                        'level_id' => $levelUnit->level->id,
                        'level_unit_id' => $levelUnit->id
                    ]);
            }

            session()->flash('status', 'Scores updated');

            return back();
            

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);

            session()->flash('error', 'A fata error occurred check with the admin');

            return back();
            
        }
        
    }
}
