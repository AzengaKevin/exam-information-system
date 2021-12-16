<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Http\Request;

class ExamsTranscriptsController extends Controller
{

    public function index(Request $request, Exam $exam)
    {
        $students = Student::whereIn('level_id', $exam->levels->pluck('id')->toArray())
            ->orderBy('level_id')
            ->paginate(24);

        return view('exams.transcripts.index', [
            'exam' => $exam,
            'students' => $students
        ]);
    }
    
}
