<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Settings\SystemSettings;
use Illuminate\Http\Request;

class LevelsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Level::class);
    }

    /**
     * Show a list view of all available levels on the application
     * 
     * @param Request $request
     * 
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Level::class);

        return view('levels.index', compact('trashed'));
    }

    /**
     * Show level individual information and some extra details about the level
     * 
     * @param Request $request
     * @param Level $level
     * @param SystemSettings $systemSettings 
     * 
     * @return View
     * 
     */
    public function show(Request $request, Level $level, SystemSettings $systemSettings)
    {
        return view('levels.show', compact('level', 'systemSettings'));
    }
}
