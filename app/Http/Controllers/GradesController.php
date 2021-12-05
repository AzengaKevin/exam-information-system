<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function index()
    {
        return view('grades.index');
    }
}
