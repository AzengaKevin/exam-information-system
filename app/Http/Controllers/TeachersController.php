<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Teacher;
use App\Models\LevelUnit;
use App\Models\Responsibility;
use App\Models\Subject;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    public function index(Request $request)
    {
        return view('teachers.index');
    }

    public function show(Teacher $teacher)
    {
        return view('teachers.show',compact('teacher'));
    }

    public function currentExamMarking(Teacher $teacher,Exam $exam)
    {

        $levelIds = $exam->levels->pluck('id');

        $levelUnitIds = LevelUnit::whereIn('id',$levelIds)->pluck('id');


        $responsibilities = $teacher->responsibilities()
            ->wherePivot('level_unit_id',$levelUnitIds)->get();        
        return view('teachers.exams.index',compact('teacher','exam','responsibilities'));
    }

    public function studentToBeScored(LevelUnit $levelUnit,Subject $subject)
    {
        return view('teachers.exams.scores',compact('levelUnit','subject'));
    }
}
