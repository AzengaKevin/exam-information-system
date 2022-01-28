<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamsActivitiesController extends Controller
{
    /**
     * Show a list of the specified exam activities
     * 
     * @param Request $Request
     * @param Exam $exam
     * @return View
     */
    public function index(Request $request, Exam $exam)
    {
        $users = $exam->userActivities()->orderByPivot('created_at', 'DESC')->paginate(24)->withQueryString();

        return view('exams.activities.index', compact('exam', 'users'));
        
    }
}
