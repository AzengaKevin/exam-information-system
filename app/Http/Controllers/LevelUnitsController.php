<?php

namespace App\Http\Controllers;

use App\Models\LevelUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class LevelUnitsController extends Controller
{

    /**
     * Creates the level unit controller instance
     */
    public function __construct() {

        $this->middleware('auth');

    }

    /**
     * Show a list | table of classes
     * 
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', LevelUnit::class);

        $trashed = $request->trashed;

        if(boolval($trashed)) $this->authorize('viewTrashed', LevelUnit::class);

        return view('level-units.index', compact('trashed'));
    }

    /**
     * Show a level unit details
     * 
     * @param LevelUnit $levelUnit
     * @return View
     */
    public function show(LevelUnit $levelUnit)
    {
        Gate::authorize('view', $levelUnit);

        return view('level-units.show',compact('levelUnit'));
    }

    /**
     * Creates LevelUnit(s) entry in the datase
     * 
     * @param Request $request
     */
    public function store(Request $request)
    {
        Gate::authorize('create', LevelUnit::class);

        try {

            DB::table('levels')
                ->crossJoin('streams')
                ->selectRaw("levels.id as level_id, streams.id as stream_id, levels.numeric AS `numeric`, streams.alias AS `alias`")
                ->whereNull('levels.deleted_at')
                ->whereNull('streams.deleted_at')
                ->get()
                ->each(function($item){

                    DB::table('level_units')
                        ->updateOrInsert([
                            'stream_id' => $item->stream_id,
                            'level_id' => $item->level_id
                        ],['alias' => "{$item->numeric}{$item->alias}"]);
                });
            
            session()->flash('status', 'Classes successfully updated');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), ['action' => __METHOD__]);
            
            session()->flash('error', 'A fatal error, occured you will have to do the rest manually');
            
        }

        return back();
        
    }
}
