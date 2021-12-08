<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeachersController extends Controller
{
    public function index(Request $request)
    {
        return view('teachers.index');
    }

    public function show(Teacher $teacher)
    {
        return view('teachers.show',compact('teacher'));
    }

}
