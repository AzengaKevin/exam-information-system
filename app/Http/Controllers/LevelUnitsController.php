<?php

namespace App\Http\Controllers;

use App\Models\LevelUnit;
use Illuminate\Http\Request;

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
}
