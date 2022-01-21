<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    /**
     * Creates the subjectsController Instance
     */
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Subject::class);
    }

    /**
     * Show a list of system subjects
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Subject::class);

        return view('subjects.index', compact('trashed'));
    }
}
