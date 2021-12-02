<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Models\Responsibility;

class ExamsScoresController extends Controller
{

    /**
     * Show a Teacher all his classes that are enrolled in an exam and he/she is
     * supposed to upload the scores
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
}
