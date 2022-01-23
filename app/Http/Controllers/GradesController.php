<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function __construct() {

        $this->middleware('auth');

        $this->authorizeResource(Grade::class);

    }

    /**
     * Show a list of all grades
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        return view('grades.index');
    }
}
