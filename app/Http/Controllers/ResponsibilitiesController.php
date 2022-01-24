<?php

namespace App\Http\Controllers;

use App\Models\Responsibility;
use Illuminate\Http\Request;

class ResponsibilitiesController extends Controller
{
    /**
     * Create ResponsibilitiesController class instance
     */
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Responsibility::class);
    }

    /**
     * Show a list of all responsibilities
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Responsibility::class);

        return view('responsibilities.index', compact('trashed'));
    }
}
