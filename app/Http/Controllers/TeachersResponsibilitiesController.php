<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeachersResponsibilitiesController extends Controller
{
    public function index(Request $request, Teacher $teacher)
    {
        return view('teachers.responsibilities.index', [
            'teacher' => $teacher
        ]);
    }
}
