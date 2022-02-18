<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentsSubjectsController extends Controller
{
    /**
     * Show a list of student subjects
     * 
     * @param Request $request
     * @param Student $student
     * @return Response
     */
    public function index(Request $request, Student $student)
    {
        /** @todo some authorization */

        return view('students.subjects.index', compact('student'));
    }
}
