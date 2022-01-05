<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Settings\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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


    public function show(Student $student, SystemSettings $systemSettings)
    {
        $access = Gate::inspect('view',$student);

        if($access->allowed()){
            return view('students.show',compact('student', 'systemSettings'));
        }else{
            session()->flash('error', $access->message());
        }
    }
}
