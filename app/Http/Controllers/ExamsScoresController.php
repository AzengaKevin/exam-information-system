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

        $examLevels = $exam->levels;

        $examSubjects = $exam->subjects;

        $responsibilities = $teacher->responsibilities()
            ->where('responsibilities.id', $responsibility->id)
            ->wherePivotIn('subject_id', $examSubjects->pluck('id')->toArray())
            ->wherePivotIn('level_unit_id', function($query) use($examLevels) {
                $query->from('level_units')
                    ->select(['level_unit_id'])
                    ->whereIn('level_id', $examLevels->pluck('id')->toArray());
            })->get();

        return view('exams.scores.index', [
            'exam' => $exam,
            'responsibilities' => $responsibilities
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

            $subject = Subject::findOrFail(intval($request->get('subject')));

            $levelUnit = LevelUnit::findOrFail(intval($request->get('level-unit')));

            // Get previous scores if available

            $scores = array();

            if (Schema::hasTable(Str::slug($exam->shortname))) {

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

            return view('exams.scores.create', [
                'subject' => $subject,
                'levelUnit' => $levelUnit,
                'exam' => $exam,
                'scores' => $scores
            ]);

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);

            abort(404, 'Either the Subject or the Level Unit has not been specified');
            
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

            foreach ($data["scores"] as $admno => $score) {
                
                DB::table(Str::slug($exam->shortname))
                    ->updateOrInsert([
                        "admno" => $admno
                    ], [
                        $subject->shortname => json_encode(
                            ['score' => $score]
                        ),
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
