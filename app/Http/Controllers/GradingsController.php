<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GradingsController extends Controller
{
    /**
     * Show a list of all grading systems available
     */
    public function index(Request $request)
    {
        return view('gradings.index');
    }
}
