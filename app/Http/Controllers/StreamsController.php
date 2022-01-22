<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use Illuminate\Http\Request;

class StreamsController extends Controller
{
    /**
     * Create a StreamsController instance
     */
    public function __construct()
    {
        $this->middleware(['auth']);

        $this->authorizeResource(Stream::class);
    }

    /**
     * Show a list of all streams
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('bulkDelete', Stream::class);

        return view('streams.index', compact('trashed'));
    }
}
