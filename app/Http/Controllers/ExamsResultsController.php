<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamsResultsController extends Controller
{

    public function index(Request $request, Exam $exam)
    {

        return view('exams.results.index', compact('exam'));
        
    }
}
