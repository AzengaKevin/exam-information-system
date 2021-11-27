<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LevelUnitsController extends Controller
{
    public function index(Request $request)
    {
        return view('level-units.index');
    }
}
