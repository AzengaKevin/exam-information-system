<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Exam::class);
    }
   
    public function index(Request $request)
    {
        return view('exams.index');
    }

    public function show(Exam $exam)
    {
        return view('exams.show', [
            'exam' => $exam
        ]);
    }
}
