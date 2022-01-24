<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamsController extends Controller
{
    /**
     * Creates an ExamController instance
     */
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Exam::class);
    }
   
    /**
     * Show a list of all the exams
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Exam::class);

        return view('exams.index', compact('trashed'));
    }

    /**
     * Show an exam detail page
     * 
     * @param Exam $exam - the exam which to show the details
     * @return View
     */
    public function show(Exam $exam)
    {
        return view('exams.show', ['exam' => $exam]);
    }
}
