<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentsController extends Controller
{

    public function __construct() {

        $this->middleware('auth');

        $this->authorizeResource(Student::class);
        
    }
    
    public function index(Request $request)
    {
        return view('students.index');
    }
}
