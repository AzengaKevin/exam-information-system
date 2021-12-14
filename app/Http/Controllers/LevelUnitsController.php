<?php

namespace App\Http\Controllers;

use App\Models\LevelUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class LevelUnitsController extends Controller
{
    public function index(Request $request)
    {
        return view('level-units.index');
    }

    public function show(LevelUnit $levelUnit)
    {
        return view('level-units.show',compact('levelUnit'));
    }

    public function store(Request $request)
    {
        $access = Gate::inspect('create', LevelUnit::class);

        if($access->allowed()){

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
                            ],[
                                'alias' => "{$item->numeric}{$item->alias}"
                            ]);
                    });
                
                session()->flash('status', 'Classes successfully updated');
                
            } catch (\Exception $exception) {

                Log::error($exception->getMessage(), [
                    'action' => __METHOD__
                ]);
                
                session()->flash('error', 'A fatal error, occured you will have to do the rest manually');
                
            }

        }else{
                
            session()->flash('error', $access->message());
            
        }

        return back();
        
    }
}
