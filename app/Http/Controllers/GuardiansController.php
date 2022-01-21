<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use Illuminate\Http\Request;

class GuardiansController extends Controller
{

    /**
     * Creates Guardians Controller Instance
     */
    public function __construct() {

        $this->middleware('auth');
        
        $this->authorizeResource(Guardian::class);

    }

    /**
     * Show a list of all guardians
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Guardian::class);

        return view('guardians.index', compact('trashed'));
    }
}
