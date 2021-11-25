<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuardiansController extends Controller
{
    public function index(Request $request)
    {
        return view('guardians.index');
    }
}
