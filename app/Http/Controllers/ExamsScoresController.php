<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadScoresRequest;
use App\Models\Exam;
use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Responsibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $this->authorize('viewScoresPage', $exam);
        
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
     * Show the table / form for uploading scores for both school with streams and ones with no streams
     * 
     * @param Request $request
     * @param Exam $exam
     */
    public function upload(Request $request, Exam $exam)
    {

        try {

            /** @var Subject */
            $subject = Subject::findOrFail(intval($request->get('subject')));

            /** @var LevelUnit */
            $levelUnit = LevelUnit::find(intval($request->get('level-unit')));

            /** @var Level */
            $level = Level::find(intval($request->get('level')));

            $gradings = Grading::all(['id', 'name']);

            $tblName = Str::slug($exam->shortname);

            $col = $subject->shortname;

            $query = DB::table('students')
                ->leftJoin("{$tblName}", "students.id", "=", "{$tblName}.student_id")
                ->select("students.id AS stid", "students.adm_no AS admno", "students.name", "{$tblName}.{$col}");

            if ($level) $query->where('students.level_id', $level->id);

            if ($levelUnit) $query->where('students.level_unit_id', $levelUnit->id);
                
            $data = $query->orderBy('students.name')->get();

            $title = "Upload " . (optional($level)->name ?? optional($levelUnit)->alias) . " - {$subject->name} Scores";

            return view('exams.scores.upload', [
                'exam' => $exam,
                'level' => $level,
                'levelUnit' => $levelUnit,
                'subject' => $subject,
                'gradings' => $gradings,
                'data' => $data,
                'title' => $title
            ]);            
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            return back();
            
        }
        
    }

    /**
     * Show class exam scores and options for extra tasks and operations
     * 
     * @param Request $request
     * @param Exam $exam
     * 
     */
    public function manage(Request $request, Exam $exam)
    {
        try {

            /** @var Subject */
            $subject = Subject::find(intval($request->get('subject')));

            /** @var LevelUnit */
            $levelUnit = LevelUnit::find(intval($request->get('level-unit')));

            /** @var Level */
            $level = Level::find(intval($request->get('level')));
            
            $title = "Manage Scores";

            if($subject){
                if($level) $title = "Upload {$exam->name} - {$level->name} - {$subject->name} Scores";
                if($levelUnit) $title = "Upload {$exam->name} - {$levelUnit->alias} - {$subject->name} Scores";
            }elseif($levelUnit){
                $title = "{$levelUnit->alias} Scores Management";
            } elseif($level){
                 $title = "{$level->name} Scores Management";
            }

            return view('exams.scores.manage', [
                'exam' => $exam,
                'level' => $level,
                'levelUnit' => $levelUnit,
                'subject' => $subject,
                'title' => $title
            ]); 

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            return back();
            
        }
        
    }

    /**
     * Persist student scores to the database
     * @param UploadScoresRequest $request
     * @param Exam $exam
     */
    public function uploadScores(UploadScoresRequest $request, Exam $exam)
    {
        /** @var array */
        $data = $request->validated();

        try {

            /** @var Subject */
            $subject = Subject::findOrFail(intval($request->get('subject')));

            /** @var LevelUnit */
            $levelUnit = LevelUnit::find(intval($request->get('level-unit')));

            /** @var Level */
            $level = Level::find(intval($request->get('level')));

            $grading = Grading::find($data['grading_id'] ?? 1) ?? Grading::first();

            $values = $grading->values;

            if(empty($subject->segments)){
                // Process Uploading the scores
                $this->uploadScoresWithoutSegments($data, $values, $level, $levelUnit, $exam, $subject);

            }else{

                $this->uploadScoresWithSegments($data, $level, $levelUnit, $exam, $subject);

            }

            session()->flash('status', 'Scores successfully updated');

            return redirect(route('exams.scores.manage', [
                'exam' => $exam,
                'subject' => $subject->id,
                'level' => optional($level)->id,
                'level-unit' => optional($levelUnit)->id
            ]));
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A db error occurred, check with admin');

            return back()->withInput();
            
        }
    }

    /**
     * Upload scores for subjects without segments
     * 
     * @param array $data request data
     * @param array $values grade, points, scores mapping
     * @param Level $level
     * @param LevelUnit $levelUnit
     * @param Exam $exam
     * @param Subject $subject
     * 
     */
    public function uploadScoresWithoutSegments(
        array $data, array $values,
        ?Level $level, ?LevelUnit $levelUnit,
        Exam $exam, Subject $subject
    )
    {
        $tblName = Str::slug($exam->shortname);
        // Process Uploading the scores
        foreach ($data["scores"] as $stid => $scoreData) {

            $score = $scoreData['score'] ?? null;
            $grade = null;
            $points = null;

            $extra = $scoreData['extra'] ?? null;

            if ($score) {
                foreach ($values as $value) {
                    if($score >= $value['min'] && $score <= $value['max']){
                        $grade = $value['grade'];
                        $points = $value['points'];
                        break;
                    }
                }
            }
            
            if ($extra) {
                $score = 0;
                $points = 0;
                switch ($extra) {
                    case 'missing':
                        $grade = 'X';
                        break;
                    case 'cheated':
                        $grade = 'Y';
                        break;
                    default:
                        $grade = 'P';
                        break;
                }
            }

            DB::table($tblName)
                ->updateOrInsert(["student_id" => $stid], [
                    $subject->shortname => json_encode([
                        'score' => intval($score),
                        'grade' => $grade,
                        'points' => intval($points),
                    ]),

                    'level_id' => optional($level)->id ?? optional($levelUnit)->level->id,
                    'level_unit_id' => optional($levelUnit)->id
                ]);
        }        
    }

    /**
     * Upload scores for subjects without segments
     * 
     * @param array $data request data
     * @param Level $level
     * @param LevelUnit $levelUnit
     * @param Exam $exam
     * @param Subject $subject
     * 
     */
    public function uploadScoresWithSegments(
        array $data, ?Level $level, ?LevelUnit $levelUnit,
        Exam $exam, Subject $subject
    )
    {
        $tblName = Str::slug($exam->shortname);

        // Process Uploading the scores
        foreach ($data["scores"] as $stid => $scoreData) {

            DB::table($tblName)
                ->updateOrInsert(["student_id" => $stid], [
                    $subject->shortname => json_encode(array_merge($scoreData, [
                        'score' => null,
                        'grade' => null,
                        'points' => null
                    ])),
                    'level_id' => optional($level)->id ?? optional($levelUnit)->level->id,
                    'level_unit_id' => optional($levelUnit)->id
                ]);
        }        
    }    

}
