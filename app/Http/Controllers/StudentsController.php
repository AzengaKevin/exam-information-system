<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Settings\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudentsController extends Controller
{

    /**
     * Creates a StudentsController instance
     */
    public function __construct() {

        $this->middleware('auth');

        $this->authorizeResource(Student::class);
        
    }
    
    /**
     * Show a list of all students in the application
     * 
     * @param Request $request
     */
    public function index(Request $request)
    {
        $trashed = $request->trashed;
        
        if(boolval($trashed)) $this->authorize('viewTrashed', Student::class);

        return view('students.index', compact('trashed'));
    }

    /**
     * Show student details page
     * 
     * @param Student $student
     * @param SystemSettings $systemSettings
     */
    public function show(Student $student, SystemSettings $systemSettings)
    {
        return view('students.show',compact('student', 'systemSettings'));
    }
}
