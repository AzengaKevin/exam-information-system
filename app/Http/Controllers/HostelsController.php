<?php

namespace App\Http\Controllers;

use App\Models\Hostel;
use Illuminate\Http\Request;

class HostelsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Hostel::class);

    }

    /**
     * Show a list of all hostels in the database
     * 
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', Hostel::class);

        return view('hostels.index', compact('trashed'));
    }

    /**
     * Show hostel details
     * 
     * @param Hostel $hostel
     */
    public function show(Hostel $hostel)
    {
        return view('hostels.show',compact('hostel'));
    }
}
