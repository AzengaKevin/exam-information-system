<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\LevelUnit;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Responsibility;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;

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

            return view('exams.scores.create', [
                'subject' => $subject,
                'levelUnit' => $levelUnit,
                'exam' => $exam
            ]);

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__,
                'exam-id' => $exam->id
            ]);

            abort(404, 'Either the Subject or the Level Unit has not been specified');
            
        }
    }
}
