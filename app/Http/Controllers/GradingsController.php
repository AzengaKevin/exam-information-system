<?php

namespace App\Http\Controllers;

use App\Models\Grading;
use Illuminate\Http\Request;

class GradingsController extends Controller
{
    /**
     * Create a GradingsController instance
     */
    public function __construct() {

        $this->middleware('auth');

        $this->authorizeResource(Grading::class);
    }
    /**
     * Show a list of all grading systems available
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Grading::class);

        return view('gradings.index', compact('trashed'));
    }
}
