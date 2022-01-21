<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    public function __construct() {

        $this->middleware('auth');

        $this->authorizeResource(Teacher::class);

    }

    /**
     * Show a list of all teachers
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Teacher::class);

        return view('teachers.index', compact('trashed'));
    }

    /**
     * Show teacher detail page
     * 
     * @param Teacher $teacher
     * @return View
     */
    public function show(Teacher $teacher)
    {
        return view('teachers.show',compact('teacher'));
    }

}
