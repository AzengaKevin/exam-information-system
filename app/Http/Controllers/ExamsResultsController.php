<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\Level;
use App\Models\LevelUnit;
use App\Settings\SystemSettings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExamsResultsController extends Controller
{

    /**
     * Show the merit list for the students in the specified levl or leve-unit using parameters
     * 
     * @param Request $request
     * @param Exam $exam
     */
    public function index(Request $request, Exam $exam)
    {
        try {

            $level = Level::find($request->get('level'));
            $levelUnit = LevelUnit::find($request->get('level-unit'));
            
            return view('exams.results.index', compact('exam', 'level', 'levelUnit'));

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), ['action' => __METHOD__]);

            $message = App::environment('local')
                ? $exception->getMessage()
                : "Something aweful occurred";

            session()->flash('error', $message);

            return back();
        }
    }

    /**
     * Send students results message to guardians where applicable
     * 
     * @param Request $request
     * @param Exam $exam
     * @param SystemSettings $systemSettings
     */
    public function sendMessage(Request $request, Exam $exam, SystemSettings $systemSettings)
    {
        /** @var User */
        $currentUser = $request->user();

        $levelUnitId = $request->get('level-unit');

        $levelId = $request->get('level');

        try {

            /** @var LevelUnit */
            $levelUnit = LevelUnit::find($levelUnitId);

            /** @var Level */
            $level = Level::find($levelId);

            // Get the student results
            $examScoresTblName = Str::slug($exam->shortname);

            $subjectColumns = $exam->subjects->pluck("shortname")->toArray();

            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");

            $studentsScoresQuery = DB::table($examScoresTblName)
                ->select(array_merge($subjectColumns, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "level_units.alias", "levels.name AS level", "hostels.name AS hostel"])
                ->addSelect(['recipient_id' => DB::table('student_guardians')
                        ->join('users', function ($join) {
                            $join->on('student_guardians.guardian_id', '=', 'users.authenticatable_id')
                                ->where('users.authenticatable_type', 'guardian');
                        })
                        ->select("users.id")
                        ->whereColumn('students.id', '=', 'student_guardians.student_id')
                        ->take('1')
                ])
                ->join("students", "{$examScoresTblName}.student_id", "=", "students.id")
                ->leftJoin("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->leftJoin("levels", "{$examScoresTblName}.level_id", "=", "levels.id")
                ->leftJoin("hostels", "students.hostel_id", "=", "hostels.id");
            
            if ($levelUnit) $studentsScoresQuery->where("{$examScoresTblName}.level_unit_id", $levelUnit->id);
            if ($level) $studentsScoresQuery->where("{$examScoresTblName}.level_id", $level->id);

            /** @var Collection */
            $studentsScores = $studentsScoresQuery->get();

            $studentsScores->each(function($studentScores) use($exam, $subjectColumns, $currentUser, $systemSettings){

                // Compose the content
                $content = "{$studentScores->name}";
                $content = "{$content},{$studentScores->adm_no}";

                $class = $studentScores->alias ?? $studentScores->level;
                $content = "{$content},{$class}";

                $content = "{$content},{$exam->name}";

                // Create the message if necessary
                foreach ($subjectColumns as $col) {
                    $upperCol = strtoupper($col);
                    $score = json_decode($studentScores->$col);
                    $content = "{$content},{$upperCol}-{$score->score}";

                    if ($systemSettings->school_level === 'secondary') $content = "{$content}{$score->grade}";
                }

                $content = "{$content},MM-{$studentScores->mm}";
                $content = "{$content},TM-{$studentScores->tm}";
                
                if ($systemSettings->school_level === 'secondary') $content = "{$content},MG-{$studentScores->mg}";
                if ($systemSettings->school_level === 'secondary') $content = "{$content},MP-{$studentScores->mp}";
                if ($systemSettings->school_level === 'secondary') $content = "{$content},TP-{$studentScores->tp}";

                if ($systemSettings->school_has_streams) $content = "{$content},SP-{$studentScores->sp}";

                $content = "{$content},OP-{$studentScores->op}";
                
                if ($studentScores->recipient_id) {
                    $currentUser->messages()->create([
                        'recipient_id' => $studentScores->recipient_id,
                        'content' => $content
                    ]);
                }

            });

            session()->flash('status', 'Results sent successfully');
            
            return back();
            
        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            abort(404, 'You tried playing tricks, don\'t');
        }

    }
}
