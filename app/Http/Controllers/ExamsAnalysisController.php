<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

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
        return view('exams.analysis.index', [
            'exam' => $exam
        ]);
        
    }
}
